<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Student;
use App\Models\Teacher;
use App\Models\StudentLogin;
use App\Models\TeacherLogin;
use Illuminate\Support\Facades\Hash;

$password = Hash::make('Abc123');

// Seed student logins
$students = Student::whereNotNull('email')->get();
$sCount = 0;
foreach ($students as $s) {
    StudentLogin::updateOrCreate(
        ['student_id' => $s->id],
        ['email' => $s->email, 'password' => $password]
    );
    $sCount++;
}
echo "Student logins created: {$sCount}\n";

// Seed teacher logins
$teachers = Teacher::whereNotNull('email')->get();
$tCount = 0;
foreach ($teachers as $t) {
    TeacherLogin::updateOrCreate(
        ['teacher_id' => $t->id],
        ['email' => $t->email, 'password' => $password]
    );
    $tCount++;
}
echo "Teacher logins created: {$tCount}\n";
echo "Password for all: Abc123\n";
echo "Done!\n";
