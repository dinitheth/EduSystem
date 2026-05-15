@extends('portal.layout')
@section('title','Results & Marks')
@section('portal-type','Teacher Portal')
@section('sidebar-bg-inline','linear-gradient(180deg,#1e1b4b 0%,#0ea5e9 100%)')
@section('logout-route', route('teacher.logout'))
@section('user-name', session('teacher_name'))
@section('user-role','Teacher')
@section('user-class', session('teacher_class'))
@section('breadcrumb','Results & Marks')

@section('sidebar-nav')
<div class="section-label">Navigation</div>
<a href="{{ route('teacher.dashboard') }}" class="nav-link"><i class="bi bi-grid-fill"></i>Dashboard</a>
<a href="{{ route('teacher.students') }}" class="nav-link"><i class="bi bi-people-fill"></i>My Students</a>
<a href="{{ route('teacher.courses') }}" class="nav-link"><i class="bi bi-book-fill"></i>Courses</a>
<a href="{{ route('teacher.assignments') }}" class="nav-link"><i class="bi bi-file-earmark-pdf-fill"></i>Assignments</a>
<a href="{{ route('teacher.mcqs') }}" class="nav-link"><i class="bi bi-patch-question-fill"></i>MCQ Tests</a>
<a href="{{ route('teacher.marks') }}" class="nav-link active"><i class="bi bi-bar-chart-fill"></i>Results & Marks</a>
@endsection

@section('content')
<h5 class="fw-bold mb-3">All Student Marks ({{ $marks->count() }})</h5>
<div class="section-card">
  <div class="table-responsive">
    <table class="table table-hover mb-0" style="font-size:.875rem;">
      <thead style="background:#f8faff;">
        <tr><th>Student</th><th>Assessment</th><th>Subject</th><th>Score</th><th>%</th><th>Grade</th><th>Date</th></tr>
      </thead>
      <tbody>
        @forelse($marks as $mk)
        @php
          $pct   = $mk->total > 0 ? round(($mk->score / $mk->total) * 100) : 0;
          $grade = $pct >= 80 ? 'A' : ($pct >= 60 ? 'B' : ($pct >= 40 ? 'C' : 'F'));
          $color = $pct >= 60 ? '#16a34a' : ($pct >= 40 ? '#f59e0b' : '#dc2626');
        @endphp
        <tr>
          <td class="fw-semibold">{{ $mk->student->full_name ?? '—' }}</td>
          <td>{{ $mk->title ?? 'MCQ' }}</td>
          <td>{{ $mk->subject->subject_name ?? 'General' }}</td>
          <td><strong>{{ $mk->score }}/{{ $mk->total }}</strong></td>
          <td><span style="font-weight:700;color:{{ $color }};">{{ $pct }}%</span></td>
          <td>
            @if($grade==='A')<span class="badge bg-success">A</span>
            @elseif($grade==='B')<span class="badge bg-primary">B</span>
            @elseif($grade==='C')<span class="badge bg-warning text-dark">C</span>
            @else<span class="badge bg-danger">F</span>@endif
          </td>
          <td class="text-muted">{{ $mk->created_at->format('d M Y') }}</td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center text-muted py-4">No marks recorded yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
