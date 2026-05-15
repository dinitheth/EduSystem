@extends('portal.layout')
@section('title','My Marks')
@section('portal-type','Student Portal')
@section('sidebar-bg-inline','linear-gradient(180deg,#0f4c75 0%,#0d7377 100%)')
@section('logout-route', route('student.logout'))
@section('user-name', session('student_name'))
@section('user-role','Student')
@section('user-class', session('student_class'))
@section('breadcrumb','My Marks')

@section('sidebar-nav')
<div class="section-label">Navigation</div>
<a href="{{ route('student.dashboard') }}" class="nav-link"><i class="bi bi-grid-fill"></i>Dashboard</a>
<a href="{{ route('student.courses') }}" class="nav-link"><i class="bi bi-book-fill"></i>Courses</a>
<a href="{{ route('student.assignments') }}" class="nav-link"><i class="bi bi-file-earmark-pdf-fill"></i>Assignments</a>
<a href="{{ route('student.mcqs') }}" class="nav-link"><i class="bi bi-patch-question-fill"></i>MCQ Tests</a>
<a href="{{ route('student.marks') }}" class="nav-link active"><i class="bi bi-bar-chart-fill"></i>My Marks</a>
@endsection

@section('content')
@php
  $avg = $marks->count() > 0 ? round($marks->sum(fn($m) => $m->total > 0 ? ($m->score/$m->total)*100 : 0) / $marks->count()) : 0;
@endphp

<div class="hello-banner mb-4" style="background:linear-gradient(135deg,#ecfdf5 0%,#e0f2fe 100%);border:1px solid #d1fae5;">
  <div>
    <h2 style="color:#0f4c75;font-size:1.5rem;">My Academic Marks</h2>
    <p style="color:#374151;margin:0;">
      Overall Average: <strong style="color:#0f766e;">{{ $avg }}%</strong>
      &nbsp;<span style="color:#9ca3af;">&middot;</span>&nbsp;
      {{ $marks->count() }} assessment(s) completed
    </p>
  </div>
</div>

<div class="section-card">
  <div class="section-header"><h5><i class="bi bi-bar-chart me-2"></i>Marks by Subject</h5></div>
  <div class="table-responsive">
    <table class="table table-hover mb-0" style="font-size:.875rem;">
      <thead style="background:#f8faff;">
        <tr><th>Assessment</th><th>Subject</th><th>Type</th><th>Score</th><th>Percentage</th><th>Grade</th><th>Date</th></tr>
      </thead>
      <tbody>
        @forelse($marks as $mk)
        @php
          $pct   = $mk->total > 0 ? round(($mk->score / $mk->total) * 100) : 0;
          $grade = $pct >= 80 ? 'A' : ($pct >= 60 ? 'B' : ($pct >= 40 ? 'C' : 'F'));
          $color = $pct >= 60 ? '#16a34a' : ($pct >= 40 ? '#f59e0b' : '#dc2626');
        @endphp
        <tr>
          <td class="fw-semibold">{{ $mk->title ?? 'MCQ Test' }}</td>
          <td>{{ $mk->subject->subject_name ?? 'General' }}</td>
          <td><span class="badge" style="background:#eef2ff;color:#6366f1;font-size:.7rem;">{{ strtoupper($mk->type) }}</span></td>
          <td><strong>{{ $mk->score }}/{{ $mk->total }}</strong></td>
          <td>
            <div class="d-flex align-items-center gap-2">
              <div style="width:80px;background:#f1f5f9;border-radius:4px;height:6px;">
                <div style="background:{{ $color }};width:{{ $pct }}%;height:6px;border-radius:4px;"></div>
              </div>
              <span style="font-weight:700;color:{{ $color }};">{{ $pct }}%</span>
            </div>
          </td>
          <td>
            @if($grade==='A')<span class="badge bg-success">A</span>
            @elseif($grade==='B')<span class="badge bg-primary">B</span>
            @elseif($grade==='C')<span class="badge bg-warning text-dark">C</span>
            @else<span class="badge bg-danger">F</span>
            @endif
          </td>
          <td class="text-muted">{{ $mk->created_at->format('d M Y') }}</td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center text-muted py-4">No marks recorded yet. Take an MCQ to get started!</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
