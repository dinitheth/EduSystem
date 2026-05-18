@extends('portal.layout')
@section('title', 'My Dashboard')
@section('portal-type', 'Student Portal')
@section('sidebar-bg-inline', 'linear-gradient(180deg,#0f4c75 0%,#0d7377 100%)')
@section('logout-route', route('student.logout'))
@section('user-name', session('student_name'))
@section('user-role', 'Student')
@section('user-class', session('student_class'))
@section('breadcrumb', 'Dashboard')

@section('sidebar-nav')
  <div class="section-label">Navigation</div>
  <a href="{{ route('student.dashboard') }}"
    class="nav-link {{ request()->routeIs('student.dashboard') ? 'active' : '' }}"><i
      class="bi bi-grid-fill"></i>Dashboard</a>
  <a href="{{ route('student.courses') }}" class="nav-link {{ request()->routeIs('student.courses') ? 'active' : '' }}"><i
      class="bi bi-book-fill"></i>Courses</a>
  <a href="{{ route('student.assignments') }}"
    class="nav-link {{ request()->routeIs('student.assignments') ? 'active' : '' }}"><i
      class="bi bi-file-earmark-pdf-fill"></i>Assignments</a>
  <a href="{{ route('student.mcqs') }}" class="nav-link {{ request()->routeIs('student.mcqs*') ? 'active' : '' }}"><i
      class="bi bi-patch-question-fill"></i>MCQ Tests</a>
  <a href="{{ route('student.marks') }}" class="nav-link {{ request()->routeIs('student.marks') ? 'active' : '' }}"><i
      class="bi bi-bar-chart-fill"></i>My Marks</a>
@endsection

@section('content')
  <div class="hello-banner" style="background:linear-gradient(135deg,#0891b2 0%,#0d9488 100%);border:none;">
    <div>
      <h2 style="color:#fff;font-size:1.5rem;font-weight:800;">Hello, {{ $student->full_name }}!</h2>
      <p style="color:rgba(255,255,255,.85);margin:0;">
        Class {{ $student->class }} Student
        &nbsp;<span style="color:rgba(255,255,255,.5);">·</span>&nbsp;
        {{ $student->subjects->count() }} subject(s) enrolled
        &nbsp;<span style="color:rgba(255,255,255,.5);">·</span>&nbsp;
        <span
          style="background:rgba(255,255,255,.2);color:#fff;padding:2px 10px;border-radius:20px;font-size:.78rem;font-weight:700;backdrop-filter:blur(4px);">{{ $student->reg_no }}</span>
      </p>
    </div>
  </div>

  {{-- Stats Row --}}
  <div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <div class="stat-icon" style="background:#eef2ff;"><i class="bi bi-book-fill" style="color:#6366f1;"></i></div>
        <div class="stat-num">{{ $student->subjects->count() }}</div>
        <div class="stat-label">Subjects</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <div class="stat-icon" style="background:#fef9c3;"><i class="bi bi-file-earmark-pdf-fill"
            style="color:#ca8a04;"></i></div>
        <div class="stat-num">{{ $assignments->count() }}</div>
        <div class="stat-label">New Assignments</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <div class="stat-icon" style="background:#f0fdf4;"><i class="bi bi-patch-question-fill"
            style="color:#16a34a;"></i></div>
        <div class="stat-num">{{ $mcqs->count() }}</div>
        <div class="stat-label">Available MCQs</div>
      </div>
    </div>
    <div class="col-6 col-md-3">
      <div class="stat-card">
        <div class="stat-icon" style="background:#fef2f2;"><i class="bi bi-bar-chart-fill" style="color:#dc2626;"></i>
        </div>
        <div class="stat-num">{{ $marks->count() }}</div>
        <div class="stat-label">Marks Recorded</div>
      </div>
    </div>
  </div>

  <div class="row g-3">
    {{-- My Subjects --}}
    <div class="col-md-4">
      <div class="section-card h-100">
        <div class="section-header">
          <h5><i class="bi bi-book me-2 text-primary"></i>My Subjects</h5>
        </div>
        <div class="p-3">
          @forelse($student->subjects as $sub)
            <div class="d-flex align-items-center gap-2 py-2 border-bottom">
              <div style="width:8px;height:8px;border-radius:50%;background:#6366f1;flex-shrink:0;"></div>
              <span style="font-size:.875rem;font-weight:500;">{{ $sub->subject_name }}</span>
              <span class="ms-auto" style="font-size:.7rem;color:#9ca3af;">{{ $sub->subject_code ?? '' }}</span>
            </div>
          @empty
            <p class="text-muted small p-2">No subjects assigned yet.</p>
          @endforelse
        </div>
      </div>
    </div>

    {{-- Recent Assignments --}}
    <div class="col-md-4">
      <div class="section-card">
        <div class="section-header">
          <h5><i class="bi bi-file-earmark-pdf me-2 text-warning"></i>Recent Assignments</h5>
          <a href="{{ route('student.assignments') }}" style="font-size:.78rem;color:#6366f1;">View All</a>
        </div>
        <div class="p-0">
          @forelse($assignments as $a)
            <div class="px-4 py-3 border-bottom">
              <div class="fw-semibold" style="font-size:.875rem;">{{ $a->title }}</div>
              <div class="text-muted" style="font-size:.75rem;">{{ $a->subject->subject_name ?? 'General' }} &nbsp;·&nbsp;
                {{ $a->teacher->full_name ?? '' }}</div>
              @if($a->due_date)
                <div style="font-size:.72rem;color:#dc2626;margin-top:2px;"><i class="bi bi-clock me-1"></i>Due:
              {{ \Carbon\Carbon::parse($a->due_date)->format('d M Y') }}</div>@endif
            </div>
          @empty
            <div class="px-4 py-3 text-muted small">No assignments yet.</div>
          @endforelse
        </div>
      </div>

      {{-- Recent MCQs --}}
      <div class="section-card mt-3">
        <div class="section-header">
          <h5><i class="bi bi-patch-question me-2 text-success"></i>Available MCQs</h5>
          <a href="{{ route('student.mcqs') }}" style="font-size:.78rem;color:#6366f1;">View All</a>
        </div>
        <div class="p-0">
          @forelse($mcqs as $m)
            <div class="px-4 py-3 border-bottom d-flex justify-content-between align-items-center">
              <div>
                <div class="fw-semibold" style="font-size:.875rem;">{{ $m->title }}</div>
                <div class="text-muted" style="font-size:.75rem;">{{ $m->subject->subject_name ?? 'General' }} &nbsp;·&nbsp;
                  {{ $m->questions_count ?? '' }}</div>
              </div>
              <a href="{{ route('student.mcq.take', $m->id) }}" class="btn btn-sm"
                style="background:#eef2ff;color:#6366f1;font-size:.75rem;font-weight:600;border-radius:8px;padding:5px 12px;">Take</a>
            </div>
          @empty
            <div class="px-4 py-3 text-muted small">No MCQs available.</div>
          @endforelse
        </div>
      </div>
    </div>

    {{-- Recent Marks --}}
    <div class="col-md-4">
      <div class="section-card h-100">
        <div class="section-header">
          <h5><i class="bi bi-bar-chart me-2 text-danger"></i>Recent Marks</h5>
          <a href="{{ route('student.marks') }}" style="font-size:.78rem;color:#6366f1;">View All</a>
        </div>
        <div class="p-0">
          @forelse($marks as $mk)
            <div class="px-4 py-3 border-bottom">
              <div class="d-flex justify-content-between">
                <span style="font-size:.85rem;font-weight:600;">{{ $mk->title ?? 'MCQ' }}</span>
                <span
                  style="font-size:.85rem;font-weight:700;color:{{ $mk->score / $mk->total >= 0.5 ? '#16a34a' : '#dc2626' }};">{{ $mk->score }}/{{ $mk->total }}</span>
              </div>
              <div class="text-muted" style="font-size:.72rem;">{{ $mk->subject->subject_name ?? 'General' }}</div>
              <div class="mt-1" style="background:#f1f5f9;border-radius:4px;height:4px;">
                <div
                  style="background:{{ $mk->score / $mk->total >= 0.5 ? '#16a34a' : '#dc2626' }};width:{{ ($mk->score / $mk->total) * 100 }}%;height:4px;border-radius:4px;">
                </div>
              </div>
            </div>
          @empty
            <div class="px-4 py-3 text-muted small">No marks recorded yet.</div>
          @endforelse
        </div>
      </div>
    </div>
  </div>
@endsection