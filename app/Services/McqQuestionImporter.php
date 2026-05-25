<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Smalot\PdfParser\Parser as PdfParser;
use Symfony\Component\Process\Process;
use ZipArchive;

class McqQuestionImporter
{
    public function import(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());

        return match ($extension) {
            'pdf' => $this->parseText($this->extractPdfText($file)),
            'docx' => $this->parseText($this->extractDocxText($file)),
            'xlsx', 'xls' => $this->extractSpreadsheetQuestions($file),
            default => throw new \InvalidArgumentException('Unsupported file type. Upload a PDF, DOCX, XLSX, or XLS file.'),
        };
    }

    private function extractPdfText(UploadedFile $file): string
    {
        return $this->normalizeText(
            $this->extractPdfTextWithPython($file->getRealPath())
                ?: (new PdfParser())->parseFile($file->getRealPath())->getText()
        );
    }

    private function extractDocxText(UploadedFile $file): string
    {
        $zip = new ZipArchive();
        if ($zip->open($file->getRealPath()) !== true) {
            throw new \RuntimeException('Could not open DOCX file.');
        }

        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        if (!$xml) {
            throw new \RuntimeException('Could not read DOCX document text.');
        }

        $xml = preg_replace('/<\/w:p>/', "\n", $xml);
        $xml = preg_replace('/<w:tab\/>/', ' ', $xml);

        return $this->normalizeText(html_entity_decode(strip_tags($xml), ENT_QUOTES | ENT_XML1, 'UTF-8'));
    }

    private function extractSpreadsheetQuestions(UploadedFile $file): array
    {
        $sheet = IOFactory::load($file->getRealPath())->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, false);
        $questions = [];
        $textLines = [];

        foreach ($rows as $row) {
            $cells = array_values(array_filter(array_map(fn($value) => $this->cleanLine((string) $value), $row), fn($value) => $value !== ''));
            if (empty($cells)) {
                continue;
            }

            if (count($cells) >= 3) {
                $question = $this->stripQuestionPrefix($cells[0]);
                $options = array_values(array_filter(array_map([$this, 'stripOptionPrefix'], array_slice($cells, 1)), fn($value) => $value !== ''));

                if ($question !== '' && count($options) >= 2) {
                    $questions[] = ['question' => $question, 'options' => array_slice($options, 0, 6)];
                    continue;
                }
            }

            $textLines[] = implode(' ', $cells);
        }

        return array_merge($questions, $this->parseText(implode("\n", $textLines)));
    }

    private function parseText(string $text): array
    {
        $text = $this->normalizeText($text);
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $lines = array_values(array_filter(array_map(fn($line) => $this->cleanLine($line), explode("\n", $text)), fn($line) => $line !== ''));
        $questions = [];
        $current = null;

        foreach ($lines as $line) {
            if ($this->isAnswerKeyLine($line)) {
                continue;
            }

            if ($this->looksLikeQuestionStart($line)) {
                $this->pushQuestion($questions, $current);
                $current = ['question' => $this->stripQuestionPrefix($line), 'options' => []];
                continue;
            }

            if ($current && $this->looksLikeOption($line)) {
                $option = $this->stripOptionPrefix($line);
                if ($option !== '') {
                    $current['options'][] = $option;
                }
                continue;
            }

            if ($current && count($current['options']) === 0) {
                $current['question'] = trim($current['question'].' '.$this->stripQuestionPrefix($line));
                continue;
            }

            if ($current && count($current['options']) > 0 && count($current['options']) < 6 && !$this->looksLikeQuestionStart($line)) {
                $current['options'][] = $this->stripOptionPrefix($line);
            }
        }

        $this->pushQuestion($questions, $current);

        return $questions;
    }

    private function pushQuestion(array &$questions, ?array $question): void
    {
        if (!$question) {
            return;
        }

        $questionText = $this->cleanLine($question['question']);
        $options = array_values(array_unique(array_filter(array_map([$this, 'cleanLine'], $question['options']), fn($option) => $option !== '')));

        if ($questionText !== '' && count($options) >= 2) {
            $questions[] = [
                'question' => $questionText,
                'options' => array_slice($options, 0, 6),
            ];
        }
    }

    private function looksLikeQuestionStart(string $line): bool
    {
        return preg_match('/^(?:q(?:uestion)?\s*)?\d{1,3}[\).:\-]\s+.+/i', $line)
            || preg_match('/^q(?:uestion)?\s*\d{1,3}\s+.+/i', $line);
    }

    private function looksLikeOption(string $line): bool
    {
        return preg_match('/^(?:[A-Ha-h]|[1-8])[\).:\-]\s+.+/', $line)
            || preg_match('/^\((?:[A-Ha-h]|[1-8])\)\s+.+/', $line);
    }

    private function stripQuestionPrefix(string $line): string
    {
        return trim(preg_replace('/^(?:q(?:uestion)?\s*)?\d{1,3}[\).:\-]?\s*/i', '', $line));
    }

    private function stripOptionPrefix(string $line): string
    {
        $line = preg_replace('/^\((?:[A-Ha-h]|[1-8])\)\s*/', '', $line);
        return trim(preg_replace('/^(?:[A-Ha-h]|[1-8])[\).:\-]\s*/', '', $line));
    }

    private function isAnswerKeyLine(string $line): bool
    {
        return preg_match('/^(?:answer|correct answer|ans|key)\b/i', $line)
            || preg_match('/^(?:answers?|answer key)\s*[:\-]/i', $line);
    }

    private function extractPdfTextWithPython(string $path): ?string
    {
        $script = <<<'PY'
import sys

path = sys.argv[1]
text = ""

try:
    from pdfminer.high_level import extract_text
    text = extract_text(path) or ""
except Exception:
    text = ""

if not text.strip():
    try:
        from pypdf import PdfReader
        reader = PdfReader(path)
        text = "\n".join(page.extract_text() or "" for page in reader.pages)
    except Exception:
        text = ""

sys.stdout.buffer.write(text.encode("utf-8", "replace"))
PY;

        foreach (['python', 'py'] as $binary) {
            try {
                $process = new Process([$binary, '-c', $script, $path], null, [
                    'PYTHONIOENCODING' => 'utf-8',
                ]);
                $process->setTimeout(30);
                $process->run();

                if ($process->isSuccessful() && trim($process->getOutput()) !== '') {
                    return $process->getOutput();
                }
            } catch (\Throwable) {
                continue;
            }
        }

        return null;
    }

    private function normalizeText(string $text): string
    {
        $text = str_replace("\0", '', $text);

        if (!mb_check_encoding($text, 'UTF-8')) {
            $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        }

        if ($this->looksLikeMojibake($text)) {
            foreach (['Windows-1252', 'ISO-8859-1'] as $encoding) {
                $repaired = @mb_convert_encoding($text, $encoding, 'UTF-8');
                if (is_string($repaired) && mb_check_encoding($repaired, 'UTF-8') && !$this->looksLikeMojibake($repaired)) {
                    $text = $repaired;
                    break;
                }
            }
        }

        return str_replace("\u{FFFD}", '?', $text);
    }

    private function cleanLine(string $line): string
    {
        $line = $this->normalizeText($line);
        $line = $this->repairIndicExtractionArtifacts($line);
        $line = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $line) ?? $line;
        $line = preg_replace('/[ \t\x{00A0}]+/u', ' ', $line) ?? $line;

        return trim($line);
    }

    private function repairIndicExtractionArtifacts(string $text): string
    {
        $text = preg_replace('/(?<=[\p{Sinhala}\p{Tamil}])[J0](?=[\p{Sinhala}\p{Tamil}])/u', '', $text) ?? $text;
        $text = preg_replace('/[J0](?=[\p{Sinhala}\p{Tamil}])/u', '', $text) ?? $text;
        $text = preg_replace('/(?<=[\p{Sinhala}\p{Tamil}])[J0]/u', '', $text) ?? $text;
        $text = preg_replace('/[\.\/](?=[\p{Sinhala}\p{Tamil}])/u', '', $text) ?? $text;

        $text = str_replace([
            'යන් නහි',
            'යන්නහි',
            'නාවන්න්',
            'වන්න්',
            'කරන්න්',
            'හාඳම',
            'හාඳින්ම',
            'ලසද',
            'වගව යුතු',
            'සංඛ්‍යා කක්‍රිමයේ',
            'ඉහළ-මට්ටම් ක්ත',
            'ඉහළ-මට්ටමේ ක්ත',
            'Queue (ාලිම)',
            'Queue (J?ාලිම)',
            'Queue (?ාලිම)',
            'වග?ව යුතු',
            '?ක්‍රියාත්මක',
            '?ක්රියාත්මක',
            '?ක්‍රියා',
            '?ක්රියා',
            'ක්රියා',
            '?රීමට',
            '�රීමට',
            '�ක්‍රියා',
            '�ක්රියා',
            'ක්ත ',
            'පරිවරේතනය',
        ], [
            'යන්නෙහි',
            'යන්නෙහි',
            'නොවන්නේ',
            'වන්නේ',
            'කරන්නේ',
            'හොඳම',
            'හොඳින්ම',
            'ලෙසද',
            'වගකිව යුතු',
            'සංඛ්‍යා ක්‍රමයේ',
            'ඉහළ-මට්ටමේ කේත',
            'ඉහළ-මට්ටමේ කේත',
            'Queue (පෝලිම)',
            'Queue (පෝලිම)',
            'Queue (පෝලිම)',
            'වගකිව යුතු',
            'ක්‍රියාත්මක',
            'ක්‍රියාත්මක',
            'ක්‍රියා',
            'ක්‍රියා',
            'ක්‍රියා',
            'කිරීමට',
            'කිරීමට',
            'ක්‍රියා',
            'ක්‍රියා',
            'කේත ',
            'පරිවර්තනය',
        ], $text);

        $text = str_replace([
            'காள்கயில்',
            'மைாழேி',
            'மைாதிரியில்',
            'பாறுப்பான',
            'திசவிடுதலுக்கு',
            'தாடர்பு',
            'முறயின்',
            'அடிப்பட ',
            'மைின்னஞ்சல்',
            'நோக்கம்',
            'நேிரலாக்கத்தில்',
            'முறமையின்',
            'மைற்றும்',
            'மைிகச்சிறிய',
            'சிக்கலானத காண்ட',
        ], [
            'கொள்கையில்',
            'மொழி',
            'மாதிரியில்',
            'பொறுப்பான',
            'திசைவிடுதலுக்கு',
            'தொடர்பு',
            'முறையின்',
            'அடிப்படை ',
            'மின்னஞ்சல்',
            'நோக்கம்',
            'நிரலாக்கத்தில்',
            'முறையின்',
            'மற்றும்',
            'மிகச்சிறிய',
            'சிக்கலானதை கொண்ட',
        ], $text);

        return $text;
    }

    private function looksLikeMojibake(string $text): bool
    {
        return str_contains($text, 'à¶')
            || str_contains($text, 'à·')
            || str_contains($text, 'à®')
            || str_contains($text, 'à¯')
            || str_contains($text, 'Â')
            || str_contains($text, 'â€');
    }
}
