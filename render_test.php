<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
// Use Console kernel — same as smoke_test.php which worked
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\StudentLogin;
use App\Models\TeacherLogin;

$sl = StudentLogin::with('student.subjects')->first();
$tl = TeacherLogin::with('teacher.subjects')->first();

$errors = [];

// Helper to render a view safely
function testView(string $name, array $data): string {
    global $errors;
    try {
        $html = view($name, $data)->render();
        echo "  OK  $name (" . number_format(strlen($html)) . " bytes)\n";
        return $html;
    } catch (\Throwable $e) {
        $msg = $e->getMessage() . " [" . basename($e->getFile()) . ":" . $e->getLine() . "]";
        echo "  ERR $name → $msg\n";
        $errors[] = "$name: $msg";
        return '';
    }
}

echo "=== BLADE RENDER TESTS ===\n\n";

// Student views
echo "--- Student views ---\n";
testView('portal.student.login', []);
testView('portal.student.dashboard', [
    'student'     => $sl->student,
    'assignments' => collect([]),
    'mcqs'        => collect([]),
    'marks'       => collect([]),
]);
testView('portal.student.courses', [
    'student'  => $sl->student,
    'subjects' => $sl->student->subjects,
]);
testView('portal.student.assignments', [
    'student'     => $sl->student,
    'assignments' => collect([]),
    'submitted'   => [],
]);
testView('portal.student.mcqs', [
    'student' => $sl->student,
    'mcqs'    => collect([]),
    'done'    => [],
]);
testView('portal.student.marks', [
    'student' => $sl->student,
    'marks'   => collect([]),
]);

// Teacher views
echo "\n--- Teacher views ---\n";
testView('portal.teacher.login', []);
testView('portal.teacher.dashboard', [
    'teacher'     => $tl->teacher,
    'assignments' => collect([]),
    'mcqs'        => collect([]),
    'results'     => collect([]),
]);
testView('portal.teacher.assignments', [
    'teacher'     => $tl->teacher,
    'assignments' => collect([]),
    'subjects'    => $tl->teacher->subjects,
]);
testView('portal.teacher.mcqs', [
    'teacher' => $tl->teacher,
    'mcqs'    => collect([]),
    'subjects'=> $tl->teacher->subjects,
]);
testView('portal.teacher.mcq_create', [
    'teacher'  => $tl->teacher,
    'subjects' => $tl->teacher->subjects,
]);
testView('portal.teacher.marks', [
    'teacher' => $tl->teacher,
    'marks'   => collect([]),
]);
testView('portal.teacher.students', [
    'teacher'  => $tl->teacher,
    'students' => collect([]),
]);
testView('portal.teacher.courses', [
    'teacher'  => $tl->teacher,
    'subjects' => $tl->teacher->subjects,
]);

echo "\n";
if (empty($errors)) {
    echo "✅ ALL VIEWS RENDER WITHOUT ERRORS\n";
} else {
    echo "❌ " . count($errors) . " view(s) had errors:\n";
    foreach ($errors as $err) echo "   - $err\n";
}

echo "\n=== TEST CREDENTIALS ===\n";
echo "Student → " . $sl->email . " / Abc123  (Class: " . $sl->student->class . ")\n";
echo "Teacher → " . $tl->email . " / Abc123  (Class: " . $tl->teacher->class . ")\n";
