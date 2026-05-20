<?php

namespace App\Services;

use App\Models\PendingStudent;
use App\Models\Student;
use App\Models\StudentLogin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class StudentAccountService
{
    public function createStudent(array $attributes, array $subjectIds = []): Student
    {
        return DB::transaction(function () use ($attributes, $subjectIds) {
            $student = Student::create([
                'reg_no' => $attributes['reg_no'] ?? $this->generateRegNo(),
                'full_name' => $attributes['full_name'],
                'email' => $attributes['email'],
                'phone' => $attributes['phone'],
                'dob' => $attributes['dob'],
                'gender' => $attributes['gender'] ?? null,
                'status' => $attributes['status'] ?? 'Active',
                'class' => $attributes['class'] ?? null,
            ]);

            $student->subjects()->sync($subjectIds);
            $this->syncLogin($student, $attributes['password'] ?? null);

            return $student;
        });
    }

    public function updateStudent(Student $student, array $attributes, array $subjectIds = []): Student
    {
        return DB::transaction(function () use ($student, $attributes, $subjectIds) {
            $student->update([
                'reg_no' => $attributes['reg_no'] ?? $student->reg_no,
                'full_name' => $attributes['full_name'],
                'email' => $attributes['email'],
                'phone' => $attributes['phone'],
                'dob' => $attributes['dob'],
                'gender' => $attributes['gender'] ?? null,
                'status' => $attributes['status'] ?? 'Active',
                'class' => $attributes['class'] ?? null,
            ]);

            $student->subjects()->sync($subjectIds);
            $this->syncLogin($student, $attributes['password'] ?? null);

            return $student;
        });
    }

    public function approvePendingStudent(PendingStudent $pendingStudent, string $class, array $subjectIds = []): Student
    {
        $student = $this->createStudent([
            'full_name' => $pendingStudent->full_name,
            'email' => $pendingStudent->email,
            'phone' => $pendingStudent->phone,
            'dob' => optional($pendingStudent->dob)->format('Y-m-d'),
            'gender' => $pendingStudent->gender,
            'status' => 'Active',
            'class' => $class,
        ], $subjectIds);

        $pendingStudent->update(['status' => 'Approved']);

        return $student;
    }

    public function generateRegNo(): string
    {
        $lastShortFormat = Student::whereRaw("reg_no REGEXP '^REG[0-9]{5}$'")
            ->selectRaw('MAX(CAST(SUBSTRING(reg_no, 4) AS UNSIGNED)) as max_reg_no')
            ->value('max_reg_no');

        $next = $lastShortFormat ? ((int) $lastShortFormat + 1) : 10001;

        return 'REG'.str_pad((string) $next, 5, '0', STR_PAD_LEFT);
    }

    private function syncLogin(Student $student, ?string $plainPassword = null): void
    {
        $login = StudentLogin::firstOrNew(['student_id' => $student->id]);
        $login->email = $student->email;

        if (!$login->exists || $plainPassword) {
            $login->password = Hash::make($plainPassword ?: 'Abc123');
        }

        $login->save();
    }
}
