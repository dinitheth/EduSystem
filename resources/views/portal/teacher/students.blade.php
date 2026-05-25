@extends('portal.layout')
@section('title','My Students')
@section('portal-type','Teacher Portal')
@section('sidebar-bg-inline','linear-gradient(180deg,#1e1b4b 0%,#0ea5e9 100%)')
@section('logout-route', route('teacher.logout'))
@section('user-name', session('teacher_name'))
@section('user-role','Teacher')
@section('user-class', session('teacher_class'))
@section('breadcrumb','My Students')

@section('sidebar-nav')
<div class="section-label">Navigation</div>
<a href="{{ route('teacher.dashboard') }}"   class="nav-link"><i class="bi bi-grid-fill"></i>Dashboard</a>
<a href="{{ route('teacher.students') }}"    class="nav-link active"><i class="bi bi-people-fill"></i>My Students</a>
<a href="{{ route('teacher.courses') }}"     class="nav-link"><i class="bi bi-book-fill"></i>Courses</a>
<a href="{{ route('teacher.assignments') }}" class="nav-link"><i class="bi bi-file-earmark-pdf-fill"></i>Assignments</a>
<a href="{{ route('teacher.mcqs') }}"        class="nav-link"><i class="bi bi-patch-question-fill"></i>MCQ Tests</a>
<a href="{{ route('teacher.marks') }}"       class="nav-link"><i class="bi bi-bar-chart-fill"></i>Results &amp; Marks</a>
@endsection

@push('styles')
<style>
.student-card{background:#fff;border-radius:12px;border:1px solid #f1f5f9;box-shadow:0 1px 4px rgba(0,0,0,.06);padding:18px 20px;transition:box-shadow .15s,transform .15s;}
.student-card:hover{box-shadow:0 4px 16px rgba(0,0,0,.1);transform:translateY(-2px);}
.student-avatar{width:46px;height:46px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:1.1rem;color:#fff;flex-shrink:0;}
.stat-pill{display:inline-flex;align-items:center;gap:4px;background:#f1f5f9;border-radius:20px;padding:3px 10px;font-size:.7rem;font-weight:600;color:#374151;}
.search-box{border:1.5px solid #e5e7eb;border-radius:10px;padding:9px 14px;font-size:.875rem;transition:border .15s;width:100%;font-family:'Inter',sans-serif;}
.search-box:focus{border-color:#6366f1;outline:none;}
.student-search-form{display:flex;gap:8px;width:100%;max-width:480px;}
.teacher-pagination .page-link{border-radius:10px;margin:0 3px;border:1px solid #dbe7f5;color:#2563eb;font-weight:700;font-size:.8rem;}
.teacher-pagination .active .page-link{background:#2563eb;border-color:#2563eb;color:#fff;}
</style>
@endpush

@section('content')
<div class="hello-banner" style="background:linear-gradient(135deg,#eff6ff 0%,#f0fdf4 100%);border:1px solid #dbeafe;">
  <div>
    <h2 style="color:#1e3a5f;font-size:1.5rem;">My Students</h2>
    <p style="color:#374151;margin:0;">
      Class {{ $teacher->class }}
      &nbsp;<span style="color:#9ca3af;">&middot;</span>&nbsp;
      <span style="background:#dbeafe;color:#1d4ed8;padding:2px 10px;border-radius:20px;font-size:.75rem;font-weight:700;">{{ $studentCount }} student(s) enrolled</span>
    </p>
  </div>
</div>

{{-- Search --}}
<div class="d-flex justify-content-between align-items-center mb-3 gap-3 flex-wrap">
  <h5 class="fw-bold mb-0" style="color:#374151;font-size:.95rem;"><i class="bi bi-people me-2 text-primary"></i>Student List — Class {{ $teacher->class }}</h5>
  <form method="GET" action="{{ route('teacher.students') }}" class="student-search-form">
    <input type="text" class="search-box" name="search" value="{{ $search }}" placeholder="Search by name, Reg No, or email...">
    <button class="btn btn-primary fw-semibold" type="submit" style="border-radius:10px;">Search</button>
    @if($search !== '')
      <a href="{{ route('teacher.students') }}" class="btn btn-outline-secondary fw-semibold" style="border-radius:10px;">Clear</a>
    @endif
  </form>
</div>

@if($students->isEmpty())
  <div class="section-card p-5 text-center text-muted">
    <i class="bi bi-people" style="font-size:2.5rem;color:#c7d2fe;"></i>
    <p class="mt-3">No students assigned to Class {{ $teacher->class }} yet.</p>
  </div>
@else
@php
$colors = ['#8b5cf6','#3b82f6','#06b6d4','#10b981','#f59e0b','#ec4899','#6366f1','#14b8a6'];
@endphp
<div class="row g-3" id="studentGrid">
  @foreach($students as $stu)
  @php $color = $colors[$loop->index % count($colors)]; $initials = strtoupper(substr($stu->full_name,0,1)); @endphp
  <div class="col-md-6 col-lg-4 student-row">
    <div class="student-card h-100">
      <div class="d-flex align-items-start gap-3">
        <div class="student-avatar" style="background:{{ $color }};">{{ $initials }}</div>
        <div class="flex-grow-1 min-width-0">
          <div class="fw-bold" style="font-size:.95rem;color:#1f2937;">{{ $stu->full_name }}</div>
          @if($stu->reg_no)
            <div style="font-size:.72rem;color:#6b7280;margin-top:1px;">
              <i class="bi bi-hash me-1"></i>{{ $stu->reg_no }}
            </div>
          @endif
          @if($stu->email)
            <div style="font-size:.72rem;color:#6b7280;word-break:break-all;">
              <i class="bi bi-envelope me-1"></i>{{ $stu->email }}
            </div>
          @endif
        </div>
        <span style="background:#e0f2fe;color:#0369a1;padding:3px 10px;border-radius:20px;font-size:.68rem;font-weight:700;flex-shrink:0;">Class {{ $stu->class }}</span>
      </div>

      {{-- Enrolled subjects --}}
      @if($stu->subjects->isNotEmpty())
      <div class="mt-3 d-flex flex-wrap gap-1">
        @foreach($stu->subjects as $sub)
        <span style="background:#eef2ff;color:#6366f1;padding:2px 10px;border-radius:20px;font-size:.65rem;font-weight:600;">{{ $sub->subject_code }}</span>
        @endforeach
      </div>
      @endif

      {{-- Stats row --}}
      <div class="d-flex gap-2 mt-3 flex-wrap">
        <span class="stat-pill"><i class="bi bi-book me-1" style="color:#6366f1;"></i>{{ $stu->subjects->count() }} subjects</span>
        @if($stu->phone)
        <span class="stat-pill"><i class="bi bi-phone me-1" style="color:#10b981;"></i>{{ $stu->phone }}</span>
        @endif
      </div>
    </div>
  </div>
  @endforeach
</div>
<div class="teacher-pagination mt-4 d-flex justify-content-center">
  {{ $students->links('pagination::bootstrap-5') }}
</div>
@endif
@endsection
