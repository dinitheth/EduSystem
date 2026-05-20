<?php

namespace App\Services;

use App\Models\Teacher;
use App\Models\TeacherLogin;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeacherAccountService
{
    public function createTeacher(array $attributes, array $subjectIds = []): Teacher
    {
        return DB::transaction(function () use ($attributes, $subjectIds) {
            $teacher = Teacher::create([
                'employee_no' => $attributes['employee_no'] ?? $this->generateEmployeeNo(),
                'full_name' => $attributes['full_name'],
                'email' => $attributes['email'],
                'phone' => $attributes['phone'],
                'specialization' => $attributes['specialization'],
                'department' => $attributes['department'] ?? null,
                'employment_status' => $attributes['employment_status'] ?? 'Full-time',
                'class' => $attributes['class'] ?? null,
            ]);

            $teacher->subjects()->sync($subjectIds);
            $this->syncLogin($teacher, $attributes['password'] ?? null);

            return $teacher;
        });
    }

    public function updateTeacher(Teacher $teacher, array $attributes, array $subjectIds = []): Teacher
    {
        return DB::transaction(function () use ($teacher, $attributes, $subjectIds) {
            $teacher->update([
                'employee_no' => $attributes['employee_no'],
                'full_name' => $attributes['full_name'],
                'email' => $attributes['email'],
                'phone' => $attributes['phone'],
                'specialization' => $attributes['specialization'],
                'department' => $attributes['department'] ?? null,
                'employment_status' => $attributes['employment_status'] ?? 'Full-time',
                'class' => $attributes['class'] ?? null,
            ]);

            $teacher->subjects()->sync($subjectIds);
            $this->syncLogin($teacher, $attributes['password'] ?? null);

            return $teacher;
        });
    }

    public function generateEmployeeNo(): string
    {
        $lastEmployeeNo = Teacher::whereRaw("employee_no REGEXP '^T[0-9]{3,5}$'")
            ->selectRaw('MAX(CAST(SUBSTRING(employee_no, 2) AS UNSIGNED)) as max_employee_no')
            ->value('max_employee_no');

        $next = $lastEmployeeNo ? ((int) $lastEmployeeNo + 1) : 100;

        return 'T'.str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }

    private function syncLogin(Teacher $teacher, ?string $plainPassword = null): void
    {
        $login = TeacherLogin::firstOrNew(['teacher_id' => $teacher->id]);
        $login->email = $teacher->email;

        if (!$login->exists || $plainPassword) {
            $login->password = Hash::make($plainPassword ?: 'Abc123');
        }

        $login->save();
    }
}
