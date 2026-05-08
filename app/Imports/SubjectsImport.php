<?php

namespace App\Imports;

use App\Models\Subject;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Collection;

class SubjectsImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    private function normalize(string $key): string
    {
        return strtolower(preg_replace('/[\s\-_]+/', '', $key));
    }

    private function find(array $row, array $aliases, $default = null)
    {
        foreach ($row as $key => $value) {
            $norm = $this->normalize((string) $key);
            foreach ($aliases as $alias) {
                if ($norm === $this->normalize($alias)) {
                    $val = trim((string) $value);
                    return $val !== '' ? $val : $default;
                }
            }
        }
        return $default;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            $row = $row->toArray();

            $subject_code = $this->find($row, ['subject_code','subjectcode','code','coursecode','course_code','subjectid','id']);
            $subject_name = $this->find($row, ['subject_name','subjectname','name','subject','title','course','coursename']);
            $description  = $this->find($row, ['description','desc','details','about','notes','summary']);

            if (!$subject_code || !$subject_name) continue;

            Subject::updateOrCreate(
                ['subject_code' => $subject_code],
                [
                    'subject_name' => $subject_name,
                    'description'  => $description,
                ]
            );
        }
    }
}
