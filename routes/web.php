<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ImportController;

Route::get('/', fn() => redirect()->route('dashboard'));

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::resource('students', StudentController::class)
    ->only(['index', 'store', 'update', 'destroy']);

Route::resource('teachers', TeacherController::class)
    ->only(['index', 'store', 'update', 'destroy']);

Route::resource('subjects', SubjectController::class)
    ->only(['index', 'store', 'update', 'destroy']);

Route::post('/export/preview', [ExportController::class, 'preview'])->name('export.preview');
Route::post('/export',         [ExportController::class, 'export'])->name('export.download');

Route::get('/import',  [ImportController::class, 'index'])->name('import.index');
Route::post('/import', [ImportController::class, 'import'])->name('import.store');
Route::post('/import/confirm', [ImportController::class, 'confirm'])->name('import.confirm');
