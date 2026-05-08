<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\Subject;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Illuminate\Support\Collection;

class StudentsImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    /**
     * Normalize a key: lowercase, strip spaces/dashes/underscores.
     */
    private function normalize(string $key): string
    {
        return strtolower(preg_replace('/[\s\-_]+/', '', $key));
    }

    /**
     * Find a value from a row by trying multiple possible column name aliases.
     */
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

            $reg_no    = $this->find($row, ['reg_no','regno','registrationno','registrationnumber','registration','studentid','studentno','id']);
            $full_name = $this->find($row, ['full_name','fullname','name','studentname','fullname']);
            $email     = $this->find($row, ['email','emailaddress','mail','email_address']);
            $phone     = $this->find($row, ['phone','phonenumber','mobile','contact','telephone','contactno']);
            $dob       = $this->find($row, ['dob','dateofbirth','birthdate','date_of_birth','birthday']);
            $subjects  = $this->find($row, ['subjects','subject','subjectnames','subjectlist','assignedsubjects']);

            if (!$reg_no || !$full_name || !$email) continue;

            $student = Student::updateOrCreate(
                ['reg_no' => $reg_no],
                [
                    'full_name' => $full_name,
                    'email'     => $email,
                    'phone'     => $phone ?? '',
                    'dob'       => $dob ? date('Y-m-d', strtotime($dob)) : null,
                ]
            );

            if ($subjects) {
                $names = array_map('trim', explode(',', $subjects));
                $ids   = Subject::whereIn('subject_name', $names)->pluck('id');
                $student->subjects()->sync($ids);
            }
        }
    }
}
