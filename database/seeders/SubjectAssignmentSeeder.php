<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class SubjectAssignmentSeeder extends Seeder
{
    public function run(): void
    {
        $ids = Subject::pluck('id')->toArray();

        if (empty($ids)) {
            $this->command->warn('No subjects found.');
            return;
        }

        Student::all()->each(function ($student) use ($ids) {
            $count = min(rand(2, 4), count($ids));
            shuffle($ids);
            $student->subjects()->sync(array_slice($ids, 0, $count));
        });
        $this->command->info('Students: subjects assigned.');

        Teacher::all()->each(function ($teacher) use ($ids) {
            $count = min(rand(1, 3), count($ids));
            shuffle($ids);
            $teacher->subjects()->sync(array_slice($ids, 0, $count));
        });
        $this->command->info('Teachers: subjects assigned.');
    }
}
