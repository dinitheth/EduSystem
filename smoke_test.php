<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\StudentLogin;
use App\Models\TeacherLogin;
use Illuminate\Support\Facades\Hash;

echo "=== STUDENT PORTAL SMOKE TEST ===\n\n";

$sl = StudentLogin::with('student.subjects')->first();
if (!$sl) { echo "ERROR: No student logins found!\n"; exit(1); }

echo "Student Name:    " . $sl->student->full_name . "\n";
echo "Student Email:   " . $sl->email . "\n";
echo "Student Class:   " . $sl->student->class . "\n";
echo "Student Subjects:" . $sl->student->subjects->pluck('subject_name')->join(', ') . "\n";
echo "Password check:  " . (Hash::check('Abc123', $sl->password) ? 'PASS' : 'FAIL') . "\n";

echo "\n=== TEACHER PORTAL SMOKE TEST ===\n\n";

$tl = TeacherLogin::with('teacher.subjects')->first();
if (!$tl) { echo "ERROR: No teacher logins found!\n"; exit(1); }

echo "Teacher Name:    " . $tl->teacher->full_name . "\n";
echo "Teacher Email:   " . $tl->email . "\n";
echo "Teacher Class:   " . $tl->teacher->class . "\n";
echo "Teacher Subjects:" . $tl->teacher->subjects->pluck('subject_name')->join(', ') . "\n";
echo "Password check:  " . (Hash::check('Abc123', $tl->password) ? 'PASS' : 'FAIL') . "\n";

echo "\n=== MCQ MODEL CAST TEST ===\n\n";
use App\Models\Mcq;
$testMcq = new Mcq(['expires_at' => now()->addMinutes(30)]);
echo "expires_at type: " . get_class($testMcq->expires_at) . "\n";
echo "isAfter(past):   " . (now()->subHour()->greaterThan($testMcq->expires_at) ? 'expired' : 'active') . "\n";

echo "\n=== DONE — Use these credentials to login ===\n";
echo "Student login → /student/login\n";
echo "  Email:    " . $sl->email . "\n";
echo "  Password: Abc123\n\n";
echo "Teacher login → /teacher/login\n";
echo "  Email:    " . $tl->email . "\n";
echo "  Password: Abc123\n";
