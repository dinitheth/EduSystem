<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\SubjectController;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('students', StudentController::class)
    ->only(['index', 'store', 'update', 'destroy']);

Route::resource('teachers', TeacherController::class)
    ->only(['index', 'store', 'update', 'destroy']);

Route::resource('subjects', SubjectController::class)
    ->only(['index', 'store', 'update', 'destroy']);
