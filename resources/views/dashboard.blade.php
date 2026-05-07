@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Overview of your school data')

@section('content')

<div class="row g-4">

    {{-- ── Students Card ── --}}
    <div class="col-md-4">
        <a href="{{ route('students.index') }}" class="stat-card" style="background: linear-gradient(135deg,#6366f1,#818cf8); color:#fff;">
            <div class="stat-icon" style="background:rgba(255,255,255,0.2);">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="stat-num">{{ $totalStudents }}</div>
            <div class="stat-label">Total Students</div>
            <div style="margin-top:14px; font-size:0.78rem; opacity:0.8;">
                <i class="bi bi-arrow-right-circle me-1"></i>View all students
            </div>
        </a>
    </div>

    {{-- ── Teachers Card ── --}}
    <div class="col-md-4">
        <a href="{{ route('teachers.index') }}" class="stat-card" style="background: linear-gradient(135deg,#0ea5e9,#38bdf8); color:#fff;">
            <div class="stat-icon" style="background:rgba(255,255,255,0.2);">
                <i class="bi bi-person-workspace"></i>
            </div>
            <div class="stat-num">{{ $totalTeachers }}</div>
            <div class="stat-label">Total Teachers</div>
            <div style="margin-top:14px; font-size:0.78rem; opacity:0.8;">
                <i class="bi bi-arrow-right-circle me-1"></i>View all teachers
            </div>
        </a>
    </div>

    {{-- ── Subjects Card ── --}}
    <div class="col-md-4">
        <a href="{{ route('subjects.index') }}" class="stat-card" style="background: linear-gradient(135deg,#10b981,#34d399); color:#fff;">
            <div class="stat-icon" style="background:rgba(255,255,255,0.2);">
                <i class="bi bi-book-fill"></i>
            </div>
            <div class="stat-num">{{ $totalSubjects }}</div>
            <div class="stat-label">Total Subjects</div>
            <div style="margin-top:14px; font-size:0.78rem; opacity:0.8;">
                <i class="bi bi-arrow-right-circle me-1"></i>View all subjects
            </div>
        </a>
    </div>

</div>

@endsection
