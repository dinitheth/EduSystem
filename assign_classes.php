<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$classes = ['A','B','C','D'];

// Assign random classes to students — evenly split
$students = App\Models\Student::all()->shuffle();
$total    = $students->count();
$chunk    = (int) ceil($total / 4);
$students->chunk($chunk)->each(function ($group, $i) use ($classes) {
    $class = $classes[$i] ?? 'D';
    $ids   = $group->pluck('id')->toArray();
    App\Models\Student::whereIn('id', $ids)->update(['class' => $class]);
});
echo "Students assigned: {$total}\n";

// Count per class
foreach ($classes as $c) {
    $cnt = App\Models\Student::where('class', $c)->count();
    echo "  Class {$c}: {$cnt} students\n";
}

// Assign random classes to teachers — evenly split
$teachers = App\Models\Teacher::all()->shuffle();
$tTotal   = $teachers->count();
$tChunk   = (int) ceil($tTotal / 4);
$teachers->chunk($tChunk)->each(function ($group, $i) use ($classes) {
    $class = $classes[$i] ?? 'D';
    $ids   = $group->pluck('id')->toArray();
    App\Models\Teacher::whereIn('id', $ids)->update(['class' => $class]);
});
echo "Teachers assigned: {$tTotal}\n";
foreach ($classes as $c) {
    $cnt = App\Models\Teacher::where('class', $c)->count();
    echo "  Class {$c}: {$cnt} teachers\n";
}
echo "Done!\n";
