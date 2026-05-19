<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Smalot\PdfParser\Parser as PdfParser;
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
        return (new PdfParser())->parseFile($file->getRealPath())->getText();
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

        return html_entity_decode(strip_tags($xml), ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    private function extractSpreadsheetQuestions(UploadedFile $file): array
    {
        $sheet = IOFactory::load($file->getRealPath())->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, false);
        $questions = [];
        $textLines = [];

        foreach ($rows as $row) {
            $cells = array_values(array_filter(array_map(fn($value) => trim((string) $value), $row), fn($value) => $value !== ''));
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
        $text = str_replace(["\r\n", "\r"], "\n", $text);
        $lines = array_values(array_filter(array_map(fn($line) => trim(preg_replace('/\s+/', ' ', $line)), explode("\n", $text)), fn($line) => $line !== ''));
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

        $questionText = trim($question['question']);
        $options = array_values(array_unique(array_filter(array_map('trim', $question['options']), fn($option) => $option !== '')));

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
}
