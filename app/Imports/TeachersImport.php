<?php

namespace App\Imports;

use App\Models\Teacher;
use App\Models\Subject;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Collection;

class TeachersImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
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

            $employee_no    = $this->find($row, ['employee_no','employeeno','empno','employeenumber','employeeid','teacherid','id','emp']);
            $full_name      = $this->find($row, ['full_name','fullname','name','teachername']);
            $email          = $this->find($row, ['email','emailaddress','mail','email_address']);
            $phone          = $this->find($row, ['phone','phonenumber','mobile','contact','telephone','contactno']);
            $specialization = $this->find($row, ['specialization','specialisation','subject','department','expertise','field']);
            $subjects       = $this->find($row, ['subjects','subjectstaught','subjectlist','taughtsubjects','assignedsubjects','subjectnames']);

            if (!$employee_no || !$full_name || !$email) continue;

            $teacher = Teacher::updateOrCreate(
                ['employee_no' => $employee_no],
                [
                    'full_name'      => $full_name,
                    'email'          => $email,
                    'phone'          => $phone ?? '',
                    'specialization' => $specialization ?? '',
                ]
            );

            if ($subjects) {
                $names = array_map('trim', explode(',', $subjects));
                $ids   = Subject::whereIn('subject_name', $names)->pluck('id');
                $teacher->subjects()->sync($ids);
            }
        }
    }
}
