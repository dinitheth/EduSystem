<?php

use App\Models\Student;
use App\Models\Teacher;
use App\Models\Subject;

$subIds = Subject::pluck('id')->toArray();

if (empty($subIds)) {
    echo "No subjects found. Please add subjects first.\n";
    exit;
}

// Assign 2-4 random subjects to each student
Student::all()->each(function ($student) use ($subIds) {
    $count = min(rand(2, 4), count($subIds));
    $keys  = array_rand($subIds, $count);
    if (!is_array($keys)) $keys = [$keys];
    $student->subjects()->sync(array_map(fn($k) => $subIds[$k], $keys));
});
echo "Students: subjects assigned.\n";

// Assign 1-3 random subjects to each teacher
Teacher::all()->each(function ($teacher) use ($subIds) {
    $count = min(rand(1, 3), count($subIds));
    $keys  = array_rand($subIds, $count);
    if (!is_array($keys)) $keys = [$keys];
    $teacher->subjects()->sync(array_map(fn($k) => $subIds[$k], $keys));
});
echo "Teachers: subjects assigned.\n";
