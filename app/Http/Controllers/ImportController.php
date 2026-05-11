<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

class ImportController extends Controller
{
    private const BATCH_SIZE = 50;

    private array $schemas = [
        'students' => [
            'label' => 'Students',
            'columns' => ['reg_no', 'full_name', 'email', 'phone', 'dob', 'gender', 'status', 'subjects'],
            'required' => ['reg_no', 'full_name', 'email', 'phone', 'dob'],
            'unique' => ['reg_no', 'email'],
            'aliases' => [
                'reg_no' => ['reg_no', 'reg no', 'regno', 'registration no', 'registration number', 'registration', 'student id', 'student no', 'id'],
                'full_name' => ['full_name', 'full name', 'fullname', 'name', 'student name'],
                'email' => ['email', 'email address', 'mail', 'email_address'],
                'phone' => ['phone', 'phone number', 'mobile', 'contact', 'telephone', 'contact no'],
                'dob' => ['dob', 'date of birth', 'birthdate', 'date_of_birth', 'birthday'],
                'gender' => ['gender', 'sex'],
                'status' => ['status', 'student status'],
                'subjects' => ['subjects', 'subject', 'subject names', 'subject list', 'assigned subjects'],
            ],
        ],
        'teachers' => [
            'label' => 'Teachers',
            'columns' => ['employee_no', 'full_name', 'email', 'phone', 'specialization', 'department', 'employment_status', 'subjects'],
            'required' => ['employee_no', 'full_name', 'email', 'phone', 'specialization'],
            'unique' => ['employee_no', 'email'],
            'aliases' => [
                'employee_no' => ['employee_no', 'employee no', 'employeeno', 'emp no', 'employee number', 'employee id', 'teacher id', 'id', 'emp'],
                'full_name' => ['full_name', 'full name', 'fullname', 'name', 'teacher name'],
                'email' => ['email', 'email address', 'mail', 'email_address'],
                'phone' => ['phone', 'phone number', 'mobile', 'contact', 'telephone', 'contact no'],
                'specialization' => ['specialization', 'specialisation', 'expertise', 'field', 'subject', 'main subject'],
                'department' => ['department', 'dept'],
                'employment_status' => ['employment_status', 'employment status', 'status', 'type', 'employment type'],
                'subjects' => ['subjects', 'subjects taught', 'subject list', 'taught subjects', 'assigned subjects', 'subject names'],
            ],
        ],
        'subjects' => [
            'label' => 'Subjects',
            'columns' => ['subject_code', 'subject_name', 'description', 'category', 'is_active'],
            'required' => ['subject_code', 'subject_name'],
            'unique' => ['subject_code'],
            'aliases' => [
                'subject_code' => ['subject_code', 'subject code', 'subjectcode', 'code', 'course code', 'course_code', 'subject id', 'id'],
                'subject_name' => ['subject_name', 'subject name', 'subjectname', 'name', 'subject', 'title', 'course', 'course name'],
                'description' => ['description', 'desc', 'details', 'about', 'notes', 'summary'],
                'category' => ['category', 'type', 'stream'],
                'is_active' => ['is_active', 'active', 'status', 'enabled'],
            ],
        ],
    ];

    public function index()
    {
        return view('import.index', [
            'preview' => session('import_preview'),
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'data_type' => 'required|in:students,teachers,subjects',
            'file' => 'required|file|mimes:csv,txt,xlsx,xls|max:10240',
        ], [
            'file.mimes' => 'Only CSV or Excel (.csv, .xlsx, .xls) files are allowed.',
            'file.max' => 'File size must be under 10 MB.',
        ]);

        $type = $request->data_type;
        $sheet = $this->readFirstSheet($request->file('file'));
        $datasetCheck = $this->assessDatasetType($sheet);

        if (!$datasetCheck['is_confident']) {
            return back()->withErrors([
                'file' => 'We could not clearly identify this file as Students, Teachers, or Subjects. Please check the uploaded headers and try again.',
            ])->withInput();
        }

        if ($datasetCheck['detected_type'] !== $type) {
            $expected = $this->schemas[$type]['label'];
            $detected = $this->schemas[$datasetCheck['detected_type']]['label'];

            return back()->withErrors([
                'file' => "Wrong dataset selected. You chose {$expected}, but this file looks like {$detected} data. Please switch the import type and try again.",
            ])->withInput();
        }

        $normalized = $this->normalizeSheet($type, $sheet);

        if ($normalized['total_rows'] === 0) {
            return back()->withErrors(['file' => 'No importable rows were found in that file.']);
        }

        $token = Str::uuid()->toString();
        $payload = [
            'type' => $type,
            'file_name' => $request->file('file')->getClientOriginalName(),
            'headers' => $normalized['headers'],
            'mapping' => $normalized['mapping'],
            'rows' => $normalized['rows'],
            'offset' => 0,
            'total_rows' => $normalized['total_rows'],
            'valid_rows' => $normalized['valid_rows'],
            'skipped_rows' => $normalized['skipped_rows'],
        ];

        $dir = storage_path('app/import-previews');
        File::ensureDirectoryExists($dir);
        File::put($this->previewPath($token), json_encode($payload));

        session(['import_preview' => $this->buildPreviewState($token, $payload)]);

        return redirect()->route('import.index');
    }

    public function confirm(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $path = $this->previewPath($request->token);
        if (!File::exists($path)) {
            return redirect()->route('import.index')->withErrors(['file' => 'Preview expired. Please upload the file again.']);
        }

        $payload = json_decode(File::get($path), true);
        $type = $payload['type'] ?? null;
        if (!isset($this->schemas[$type])) {
            return redirect()->route('import.index')->withErrors(['file' => 'Invalid import preview. Please upload the file again.']);
        }

        $offset = (int) ($payload['offset'] ?? 0);
        $batchRows = array_slice($payload['rows'] ?? [], $offset, self::BATCH_SIZE);
        if (empty($batchRows)) {
            File::delete($path);
            session()->forget('import_preview');
            return redirect()->route('import.index')->withErrors(['file' => 'No batch rows found. Please upload the file again.']);
        }

        $result = DB::transaction(fn () => $this->storeRows($type, $batchRows));
        $nextOffset = $offset + count($batchRows);

        if ($nextOffset >= count($payload['rows'] ?? [])) {
            File::delete($path);
            session()->forget('import_preview');

            return redirect()->route('import.index')
                ->with('success', "Import complete. {$result['imported']} row(s) imported and {$result['skipped']} row(s) skipped in the final batch.");
        }

        $payload['offset'] = $nextOffset;
        File::put($path, json_encode($payload));
        session(['import_preview' => $this->buildPreviewState($request->token, $payload)]);

        return redirect()->route('import.index')
            ->with('success', "Batch saved. {$result['imported']} row(s) imported and {$result['skipped']} row(s) skipped. Review the next 50 rows.");
    }

    public function clear(Request $request)
    {
        $token = $request->input('token') ?: data_get(session('import_preview'), 'token');

        if ($token && File::exists($this->previewPath($token))) {
            File::delete($this->previewPath($token));
        }

        session()->forget('import_preview');

        return redirect()->route('import.index')->with('success', 'Import preview cleared. You can upload a new file now.');
    }

    private function buildPreviewState(string $token, array $payload): array
    {
        $rows = $payload['rows'] ?? [];
        $offset = (int) ($payload['offset'] ?? 0);
        $batchRows = array_slice($rows, $offset, self::BATCH_SIZE);
        $batchValidRows = count(array_filter($batchRows, fn ($row) => !empty($row['_valid'])));
        $batchSkippedRows = count($batchRows) - $batchValidRows;

        return [
            'token' => $token,
            'type' => $payload['type'],
            'label' => $this->schemas[$payload['type']]['label'],
            'columns' => $this->schemas[$payload['type']]['columns'],
            'required' => $this->schemas[$payload['type']]['required'],
            'headers' => $payload['headers'] ?? [],
            'mapping' => $payload['mapping'] ?? [],
            'rows' => $batchRows,
            'total_rows' => $payload['total_rows'] ?? count($rows),
            'valid_rows' => $payload['valid_rows'] ?? 0,
            'skipped_rows' => $payload['skipped_rows'] ?? 0,
            'file_name' => $payload['file_name'] ?? 'uploaded file',
            'offset' => $offset,
            'batch_size' => count($batchRows),
            'batch_valid_rows' => $batchValidRows,
            'batch_skipped_rows' => $batchSkippedRows,
            'batch_start' => $offset + 1,
            'batch_end' => $offset + count($batchRows),
            'has_more_batches' => ($offset + count($batchRows)) < count($rows),
            'batch_number' => (int) floor($offset / self::BATCH_SIZE) + 1,
            'batch_total' => (int) ceil(max(count($rows), 1) / self::BATCH_SIZE),
        ];
    }

    private function previewPath(string $token): string
    {
        return storage_path('app/import-previews' . DIRECTORY_SEPARATOR . $token . '.json');
    }

    private function readFirstSheet($file): array
    {
        $sheets = Excel::toArray(new class implements ToArray {
            public function array(array $array)
            {
                return $array;
            }
        }, $file);

        return $sheets[0] ?? [];
    }

    private function normalizeSheet(string $type, array $sheet): array
    {
        $schema = $this->schemas[$type];
        $headerIndex = $this->detectHeaderRow($sheet, $schema);
        $headers = array_map(fn ($value) => trim((string) $value), $sheet[$headerIndex] ?? []);
        $mapping = $this->buildMapping($headers, $schema);
        $rows = [];
        $validRows = 0;
        $skippedRows = 0;
        $seen = [];

        foreach (array_slice($sheet, $headerIndex + 1) as $rowIndex => $row) {
            if ($this->isEmptyRow($row)) {
                continue;
            }

            $normalized = [];
            foreach ($schema['columns'] as $column) {
                $index = $mapping[$column]['index'];
                $normalized[$column] = $index === null ? '' : $this->cleanValue($row[$index] ?? '');
            }

            $normalized = $this->coerceRow($type, $normalized);
            $previewRowNumber = $headerIndex + 2 + $rowIndex;
            $errors = $this->validatePreviewRow($type, $normalized, $seen, $previewRowNumber);
            $normalized['_errors'] = $errors;
            $normalized['_valid'] = empty($errors);
            $normalized['_source_row'] = $previewRowNumber;
            $rows[] = $normalized;

            if (empty($errors)) {
                $validRows++;
            } else {
                $skippedRows++;
            }
        }

        return [
            'headers' => $headers,
            'mapping' => $mapping,
            'rows' => $rows,
            'total_rows' => count($rows),
            'valid_rows' => $validRows,
            'skipped_rows' => $skippedRows,
        ];
    }

    private function assessDatasetType(array $sheet): array
    {
        $scores = [];

        foreach ($this->schemas as $type => $schema) {
            $headerIndex = $this->detectHeaderRow($sheet, $schema);
            $headers = array_map(fn ($value) => trim((string) $value), $sheet[$headerIndex] ?? []);
            $matchedColumns = [];
            $requiredMatches = 0;
            $uniqueMatches = 0;

            foreach ($schema['columns'] as $column) {
                $aliases = array_map([$this, 'normalizeKey'], $schema['aliases'][$column] ?? []);

                foreach ($headers as $header) {
                    if (in_array($this->normalizeKey($header), $aliases, true)) {
                        $matchedColumns[] = $column;

                        if (in_array($column, $schema['required'], true)) {
                            $requiredMatches++;
                        }

                        if (in_array($column, $schema['unique'] ?? [], true)) {
                            $uniqueMatches++;
                        }

                        break;
                    }
                }
            }

            $scores[$type] = [
                'matched_columns' => array_values(array_unique($matchedColumns)),
                'matched_count' => count(array_unique($matchedColumns)),
                'required_matches' => $requiredMatches,
                'unique_matches' => $uniqueMatches,
            ];
        }

        $bestType = null;
        $bestScore = -1;
        $bestRequired = -1;
        $bestUnique = -1;

        foreach ($scores as $type => $score) {
            if (
                $score['matched_count'] > $bestScore ||
                ($score['matched_count'] === $bestScore && $score['required_matches'] > $bestRequired) ||
                ($score['matched_count'] === $bestScore && $score['required_matches'] === $bestRequired && $score['unique_matches'] > $bestUnique)
            ) {
                $bestType = $type;
                $bestScore = $score['matched_count'];
                $bestRequired = $score['required_matches'];
                $bestUnique = $score['unique_matches'];
            }
        }

        $isConfident = $bestType !== null
            && $bestScore >= 2
            && $bestRequired >= 2
            && $bestUnique >= 1;

        return [
            'detected_type' => $bestType,
            'scores' => $scores,
            'is_confident' => $isConfident,
        ];
    }

    private function detectHeaderRow(array $sheet, array $schema): int
    {
        $bestIndex = 0;
        $bestScore = -1;

        foreach (array_slice($sheet, 0, 10, true) as $index => $row) {
            $score = 0;
            foreach ($row as $cell) {
                $key = $this->normalizeKey((string) $cell);
                foreach ($schema['aliases'] as $aliases) {
                    if (in_array($key, array_map([$this, 'normalizeKey'], $aliases), true)) {
                        $score++;
                    }
                }
            }

            if ($score > $bestScore) {
                $bestScore = $score;
                $bestIndex = (int) $index;
            }
        }

        return $bestScore > 0 ? $bestIndex : 0;
    }

    private function buildMapping(array $headers, array $schema): array
    {
        $mapping = [];

        foreach ($schema['columns'] as $position => $column) {
            $mapping[$column] = ['header' => null, 'index' => null];

            foreach ($headers as $index => $header) {
                $normalizedHeader = $this->normalizeKey($header);
                $aliases = array_map([$this, 'normalizeKey'], $schema['aliases'][$column] ?? []);

                if (in_array($normalizedHeader, $aliases, true)) {
                    $mapping[$column] = ['header' => $header, 'index' => $index];
                    break;
                }
            }

            if ($mapping[$column]['index'] === null && array_key_exists($position, $headers)) {
                $mapping[$column] = ['header' => $headers[$position], 'index' => $position];
            }
        }

        return $mapping;
    }

    private function storeRows(string $type, array $rows): array
    {
        $imported = 0;
        $skipped = 0;

        foreach ($rows as $row) {
            $cleanRow = $row;
            unset($cleanRow['_valid'], $cleanRow['_errors'], $cleanRow['_source_row']);
            $seen = [];

            if (!empty($this->validatePreviewRow($type, $cleanRow, $seen, null, true))) {
                $skipped++;
                continue;
            }

            match ($type) {
                'students' => $this->storeStudent($cleanRow),
                'teachers' => $this->storeTeacher($cleanRow),
                'subjects' => $this->storeSubject($cleanRow),
            };

            $imported++;
        }

        return compact('imported', 'skipped');
    }

    private function storeStudent(array $row): void
    {
        $student = Student::create([
            'reg_no' => $row['reg_no'],
            'full_name' => $row['full_name'],
            'email' => $row['email'],
            'phone' => $row['phone'],
            'dob' => $row['dob'],
            'gender' => $row['gender'] ?: null,
            'status' => $row['status'] ?: 'Active',
        ]);

        $this->syncSubjects($student, $row['subjects'] ?? '');
    }

    private function storeTeacher(array $row): void
    {
        $teacher = Teacher::create([
            'employee_no' => $row['employee_no'],
            'full_name' => $row['full_name'],
            'email' => $row['email'],
            'phone' => $row['phone'],
            'specialization' => $row['specialization'],
            'department' => $row['department'] ?: null,
            'employment_status' => $row['employment_status'] ?: 'Full-time',
        ]);

        $this->syncSubjects($teacher, $row['subjects'] ?? '');
    }

    private function storeSubject(array $row): void
    {
        Subject::create([
            'subject_code' => $row['subject_code'],
            'subject_name' => $row['subject_name'],
            'description' => $row['description'] ?: null,
            'category' => $row['category'] ?: null,
            'is_active' => $this->toBoolean($row['is_active']),
        ]);
    }

    private function syncSubjects($model, string $subjects): void
    {
        if (trim($subjects) === '') {
            $model->subjects()->sync([]);
            return;
        }

        $names = array_filter(array_map('trim', preg_split('/[,;|]/', $subjects)));
        $ids = Subject::whereIn('subject_name', $names)
            ->orWhereIn('subject_code', $names)
            ->pluck('id');

        $model->subjects()->sync($ids);
    }

    private function coerceRow(string $type, array $row): array
    {
        if ($type === 'students') {
            $row['dob'] = $this->toDate($row['dob']);
            $row['gender'] = $this->normalizeChoice($row['gender'], ['Male', 'Female', 'Other']);
            $row['status'] = $this->normalizeChoice($row['status'], ['Active', 'Inactive']) ?: 'Active';
        }

        if ($type === 'teachers') {
            $row['employment_status'] = $this->normalizeChoice($row['employment_status'], ['Full-time', 'Part-time', 'Contract']) ?: 'Full-time';
        }

        if ($type === 'subjects') {
            $row['is_active'] = $row['is_active'] === '' ? '1' : ($this->toBoolean($row['is_active']) ? '1' : '0');
        }

        return $row;
    }

    private function validatePreviewRow(
        string $type,
        array $row,
        array &$seen = [],
        ?int $sourceRow = null,
        bool $checkDatabase = true
    ): array
    {
        $schema = $this->schemas[$type];
        $errors = [];

        foreach ($schema['required'] as $column) {
            if (trim((string) ($row[$column] ?? '')) === '') {
                $errors[] = $column . ' is required';
            }
        }

        if (!empty($row['email']) && !filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'email is invalid';
        }

        foreach ($schema['unique'] ?? [] as $column) {
            $value = trim((string) ($row[$column] ?? ''));
            if ($value === '') {
                continue;
            }

            $seenKey = strtolower($value);
            if (isset($seen[$column][$seenKey])) {
                $errors[] = $column . ' is duplicated in file (also row ' . $seen[$column][$seenKey] . ')';
            } elseif ($sourceRow !== null) {
                $seen[$column][$seenKey] = $sourceRow;
            }
        }

        if ($checkDatabase) {
            $errors = array_merge($errors, $this->databaseConflictErrors($type, $row));
        }

        return $errors;
    }

    private function databaseConflictErrors(string $type, array $row): array
    {
        $schema = $this->schemas[$type];
        $modelClass = match ($type) {
            'students' => Student::class,
            'teachers' => Teacher::class,
            'subjects' => Subject::class,
        };

        $errors = [];

        foreach ($schema['unique'] ?? [] as $column) {
            $value = trim((string) ($row[$column] ?? ''));
            if ($value === '') {
                continue;
            }

            $existing = $modelClass::query()->where($column, $value)->first();
            if (!$existing) {
                continue;
            }

            $errors[] = $column . ' already exists in database (' . $value . ')';
        }

        return $errors;
    }

    private function cleanValue($value): string
    {
        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        if (is_float($value) || is_int($value)) {
            return trim((string) $value);
        }

        return trim((string) $value);
    }

    private function toDate($value): string
    {
        if ($value === '') {
            return '';
        }

        if (is_numeric($value) && (int) $value > 20000) {
            try {
                return ExcelDate::excelToDateTimeObject((float) $value)->format('Y-m-d');
            } catch (\Throwable) {
                return '';
            }
        }

        $timestamp = strtotime((string) $value);
        return $timestamp ? date('Y-m-d', $timestamp) : '';
    }

    private function toBoolean($value): bool
    {
        return in_array($this->normalizeKey((string) $value), ['1', 'yes', 'true', 'active', 'enabled', 'on'], true);
    }

    private function normalizeChoice($value, array $allowed): string
    {
        $normalized = $this->normalizeKey((string) $value);

        foreach ($allowed as $choice) {
            if ($normalized === $this->normalizeKey($choice)) {
                return $choice;
            }
        }

        return '';
    }

    private function normalizeKey(string $value): string
    {
        return preg_replace('/[^a-z0-9]+/', '', strtolower($value));
    }

    private function isEmptyRow(array $row): bool
    {
        return collect($row)->filter(fn ($value) => trim((string) $value) !== '')->isEmpty();
    }
}
