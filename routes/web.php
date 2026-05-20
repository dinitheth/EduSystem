<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\SubjectController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\PendingStudentController;
use App\Http\Controllers\PortalNotificationController;
use App\Http\Controllers\Portal\StudentAuthController;
use App\Http\Controllers\Portal\TeacherAuthController;
use App\Http\Controllers\Portal\StudentPortalController;
use App\Http\Controllers\Portal\TeacherPortalController;
use App\Http\Controllers\WebsiteController;

Route::get('/', [WebsiteController::class, 'home'])->name('website.home');
Route::get('/subjects/suggestions', [WebsiteController::class, 'subjectSuggestions'])->name('website.subjects.suggestions');
Route::get('/register-interest', fn () => redirect()->to(route('website.home').'#register'));
Route::post('/register-interest', [WebsiteController::class, 'register'])->name('website.register');

Route::get('/admin/login',   [AdminAuthController::class, 'showLogin'])->name('admin.login');
Route::post('/admin/login',  [AdminAuthController::class, 'login'])->name('admin.login.post');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

Route::middleware('admin.auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('students', StudentController::class)->only(['index','store','update','destroy']);
    Route::resource('teachers', TeacherController::class)->only(['index','store','update','destroy']);
    Route::resource('subjects',  SubjectController::class)->only(['index','store','update','destroy']);
    Route::get('/pending-students', [PendingStudentController::class, 'index'])->name('pending-students.index');
    Route::post('/pending-students/{pendingStudent}/approve', [PendingStudentController::class, 'approve'])->name('pending-students.approve');
    Route::post('/pending-students/{pendingStudent}/dismiss', [PendingStudentController::class, 'dismiss'])->name('pending-students.dismiss');

    Route::post('/export/preview', [ExportController::class, 'preview'])->name('export.preview');
    Route::post('/export',         [ExportController::class, 'export'])->name('export.download');

    Route::get('/import',          [ImportController::class, 'index'])->name('import.index');
    Route::post('/import',         [ImportController::class, 'import'])->name('import.store');
    Route::post('/import/confirm', [ImportController::class, 'confirm'])->name('import.confirm');
    Route::post('/import/clear',   [ImportController::class, 'clear'])->name('import.clear');
});

// ── Student Portal ──────────────────────────────────────────────
Route::get('/student/login',   [StudentAuthController::class, 'showLogin'])->name('student.login');
Route::post('/student/login',  [StudentAuthController::class, 'login'])->name('student.login.post');
Route::post('/student/logout', [StudentAuthController::class, 'logout'])->name('student.logout');

Route::prefix('student')->middleware('student.auth')->group(function () {
    Route::get('/notifications/{notification}/open', [PortalNotificationController::class, 'open'])->name('student.notifications.open');
    Route::get('/dashboard',                             [StudentPortalController::class, 'dashboard'])->name('student.dashboard');
    Route::get('/courses',                               [StudentPortalController::class, 'courses'])->name('student.courses');
    Route::get('/courses/{subject}/content',             [StudentPortalController::class, 'courseContent'])->name('student.course.content');
    Route::get('/assignments',                           [StudentPortalController::class, 'assignments'])->name('student.assignments');
    Route::post('/assignments/{assignment}/submit',      [StudentPortalController::class, 'submitAssignment'])->name('student.assignment.submit');
    Route::get('/mcqs',                                  [StudentPortalController::class, 'mcqs'])->name('student.mcqs');
    Route::get('/mcqs/{mcq}',                            [StudentPortalController::class, 'takeMcq'])->name('student.mcq.take');
    Route::post('/mcqs/{mcq}/submit',                    [StudentPortalController::class, 'submitMcq'])->name('student.mcq.submit');
    Route::get('/results/{submission}',                  [StudentPortalController::class, 'mcqResult'])->name('student.mcq.result');
    Route::get('/marks',                                 [StudentPortalController::class, 'marks'])->name('student.marks');
});

// ── Teacher Portal ──────────────────────────────────────────────
Route::get('/teacher/login',   [TeacherAuthController::class, 'showLogin'])->name('teacher.login');
Route::post('/teacher/login',  [TeacherAuthController::class, 'login'])->name('teacher.login.post');
Route::post('/teacher/logout', [TeacherAuthController::class, 'logout'])->name('teacher.logout');

Route::prefix('teacher')->middleware('teacher.auth')->group(function () {
    Route::get('/notifications/{notification}/open', [PortalNotificationController::class, 'open'])->name('teacher.notifications.open');
    Route::get('/dashboard',                              [TeacherPortalController::class, 'dashboard'])->name('teacher.dashboard');
    Route::get('/students',                               [TeacherPortalController::class, 'students'])->name('teacher.students');
    Route::get('/courses',                                [TeacherPortalController::class, 'courses'])->name('teacher.courses');
    Route::get('/courses/{subject}/content',              [TeacherPortalController::class, 'courseContent'])->name('teacher.course.content');
    Route::post('/courses/{subject}/content',             [TeacherPortalController::class, 'storeCourseContent'])->name('teacher.course.content.store');
    Route::delete('/courses/{subject}/content/{content}', [TeacherPortalController::class, 'deleteCourseContent'])->name('teacher.course.content.delete');
    Route::get('/assignments',                            [TeacherPortalController::class, 'assignments'])->name('teacher.assignments');
    Route::post('/assignments',                           [TeacherPortalController::class, 'storeAssignment'])->name('teacher.assignments.store');
    Route::put('/assignments/{assignment}',               [TeacherPortalController::class, 'updateAssignment'])->name('teacher.assignments.update');
    Route::delete('/assignments/{assignment}',            [TeacherPortalController::class, 'deleteAssignment'])->name('teacher.assignments.delete');
    Route::get('/assignments/{assignment}/submissions',   [TeacherPortalController::class, 'viewSubmissions'])->name('teacher.assignment.submissions');
    Route::post('/assignments/{assignment}/submissions/{submission}/grade', [TeacherPortalController::class, 'gradeSubmission'])->name('teacher.assignment.grade');
    Route::get('/assignments/{assignment}/submissions/download-all', [TeacherPortalController::class, 'downloadAllSubmissions'])->name('teacher.assignment.submissions.download');
    Route::get('/assignments/{assignment}/submissions/excel', [TeacherPortalController::class, 'downloadSubmissionsExcel'])->name('teacher.assignment.submissions.excel');
    Route::get('/mcqs',                                   [TeacherPortalController::class, 'mcqs'])->name('teacher.mcqs');
    Route::get('/mcqs/create',                            [TeacherPortalController::class, 'createMcq'])->name('teacher.mcq.create');
    Route::post('/mcqs/import',                           [TeacherPortalController::class, 'importMcqQuestions'])->name('teacher.mcq.import');
    Route::post('/mcqs',                                  [TeacherPortalController::class, 'storeMcq'])->name('teacher.mcq.store');
    Route::get('/mcqs/{mcq}/results',                     [TeacherPortalController::class, 'results'])->name('teacher.mcq.results');
    Route::get('/marks',                                  [TeacherPortalController::class, 'marks'])->name('teacher.marks');
});
