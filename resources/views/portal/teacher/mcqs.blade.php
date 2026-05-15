@extends('portal.layout')
@section('title','MCQ Tests')
@section('portal-type','Teacher Portal')
@section('sidebar-bg-inline','linear-gradient(180deg,#1e1b4b 0%,#0ea5e9 100%)')
@section('logout-route', route('teacher.logout'))
@section('user-name', session('teacher_name'))
@section('user-role','Teacher')
@section('user-class', session('teacher_class'))
@section('breadcrumb','MCQ Tests')

@section('sidebar-nav')
<div class="section-label">Navigation</div>
<a href="{{ route('teacher.dashboard') }}" class="nav-link"><i class="bi bi-grid-fill"></i>Dashboard</a>
<a href="{{ route('teacher.students') }}" class="nav-link"><i class="bi bi-people-fill"></i>My Students</a>
<a href="{{ route('teacher.courses') }}" class="nav-link"><i class="bi bi-book-fill"></i>Courses</a>
<a href="{{ route('teacher.assignments') }}" class="nav-link"><i class="bi bi-file-earmark-pdf-fill"></i>Assignments</a>
<a href="{{ route('teacher.mcqs') }}" class="nav-link active"><i class="bi bi-patch-question-fill"></i>MCQ Tests</a>
<a href="{{ route('teacher.marks') }}" class="nav-link"><i class="bi bi-bar-chart-fill"></i>Results & Marks</a>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h5 class="fw-bold mb-0">MCQ Tests ({{ $mcqs->count() }})</h5>
  <a href="{{ route('teacher.mcq.create') }}" class="btn fw-semibold" style="background:linear-gradient(135deg,#6366f1,#3730a3);color:#fff;border-radius:10px;font-size:.875rem;padding:8px 20px;">
    <i class="bi bi-plus-circle me-2"></i>Create MCQ
  </a>
</div>
<div class="section-card">
  <div class="table-responsive">
    <table class="table table-hover mb-0" style="font-size:.875rem;">
      <thead style="background:#f8faff;">
        <tr><th>Title</th><th>Class</th><th>Subject</th><th>Questions</th><th>Time</th><th>Submissions</th><th>Actions</th></tr>
      </thead>
      <tbody>
        @forelse($mcqs as $m)
        <tr>
          <td class="fw-semibold">{{ $m->title }}</td>
          <td><span class="badge-class">{{ $m->class }}</span></td>
          <td>{{ $m->subject->subject_name ?? 'General' }}</td>
          <td>{{ $m->questions_count ?? $m->questions()->count() }} Qs</td>
          <td>{{ $m->time_limit ? $m->time_limit.' min' : '—' }}</td>
          <td>
            <span class="badge rounded-pill bg-primary">{{ $m->submissions->count() }}</span>
          </td>
          <td>
            <a href="{{ route('teacher.mcq.results', $m->id) }}" class="btn btn-sm" style="background:#eef2ff;color:#6366f1;font-size:.75rem;border-radius:8px;padding:5px 12px;">
              <i class="bi bi-eye me-1"></i>Results
            </a>
          </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center text-muted py-4">No MCQ tests yet. <a href="{{ route('teacher.mcq.create') }}">Create one now →</a></td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
