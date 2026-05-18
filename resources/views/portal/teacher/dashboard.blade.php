@extends('portal.layout')
@section('title', 'Teacher Dashboard')
@section('portal-type', 'Teacher Portal')
@section('sidebar-bg-inline', 'linear-gradient(180deg,#1e1b4b 0%,#0ea5e9 100%)')
@section('logout-route', route('teacher.logout'))
@section('user-name', session('teacher_name'))
@section('user-role', 'Teacher')
@section('user-class', session('teacher_class'))
@section('breadcrumb', 'Dashboard')

@section('sidebar-nav')
  <div class="section-label">Navigation</div>
  <a href="{{ route('teacher.dashboard') }}"
    class="nav-link {{ request()->routeIs('teacher.dashboard') ? 'active' : '' }}"><i
      class="bi bi-grid-fill"></i>Dashboard</a>
  <a href="{{ route('teacher.students') }}" class="nav-link {{ request()->routeIs('teacher.students') ? 'active' : '' }}"><i
      class="bi bi-people-fill"></i>My Students</a>
  <a href="{{ route('teacher.courses') }}" class="nav-link {{ request()->routeIs('teacher.courses*') ? 'active' : '' }}"><i
      class="bi bi-book-fill"></i>Courses</a>
  <a href="{{ route('teacher.assignments') }}"
    class="nav-link {{ request()->routeIs('teacher.assignments') ? 'active' : '' }}"><i
      class="bi bi-file-earmark-pdf-fill"></i>Assignments</a>
  <a href="{{ route('teacher.mcqs') }}" class="nav-link {{ request()->routeIs('teacher.mcqs*') ? 'active' : '' }}"><i
      class="bi bi-patch-question-fill"></i>MCQ Tests</a>
  <a href="{{ route('teacher.marks') }}" class="nav-link {{ request()->routeIs('teacher.marks') ? 'active' : '' }}"><i
      class="bi bi-bar-chart-fill"></i>Results & Marks</a>
@endsection

@section('content')
  <div class="hello-banner" style="background:linear-gradient(135deg,#4f46e5 0%,#0891b2 100%);border:none;">
    <div>
      <h2 style="color:#fff;font-size:1.5rem;font-weight:800;">Hello, {{ $teacher->full_name }}!</h2>
      <p style="color:rgba(255,255,255,.85);margin:0;">
        Class {{ $teacher->class }} Teacher
        &nbsp;<span style="color:rgba(255,255,255,.5);">&middot;</span>&nbsp;
        {{ $teacher->specialization }}
        @if($teacher->department)
          &nbsp;<span style="color:rgba(255,255,255,.5);">&middot;</span>&nbsp;{{ $teacher->department }}
        @endif
        @if($teacher->employee_no)
          &nbsp;<span
            style="background:rgba(255,255,255,.2);color:#fff;padding:2px 10px;border-radius:20px;font-size:.75rem;font-weight:700;backdrop-filter:blur(4px);">EMP
            {{ $teacher->employee_no }}</span>
        @endif
      </p>
    </div>
  </div>

  {{-- Stat cards + small action buttons --}}
  <div class="row g-3 mb-4">
    <div class="col-md-6">
      <a href="{{ route('teacher.assignments') }}" class="text-decoration-none">
        <div class="stat-card d-flex align-items-center gap-3" style="border-left:4px solid #f59e0b;">
          <div class="stat-icon" style="background:#fef9c3;margin:0;"><i class="bi bi-file-earmark-pdf-fill"
              style="color:#ca8a04;"></i></div>
          <div>
            <div class="stat-num" style="font-size:1.3rem;">{{ $assignmentCount }}</div>
            <div class="stat-label">Posted Assignments</div>
          </div>
          <a href="{{ route('teacher.assignments') }}" class="btn btn-sm ms-auto fw-semibold"
            style="background:#fef3c7;color:#92400e;border-radius:8px;font-size:.72rem;padding:5px 12px;white-space:nowrap;">
            <i class="bi bi-upload me-1"></i>Post New
          </a>
        </div>
      </a>
    </div>
    <div class="col-md-6">
      <a href="{{ route('teacher.mcqs') }}" class="text-decoration-none">
        <div class="stat-card d-flex align-items-center gap-3" style="border-left:4px solid #6366f1;">
          <div class="stat-icon" style="background:#eef2ff;margin:0;"><i class="bi bi-patch-question-fill"
              style="color:#6366f1;"></i></div>
          <div>
            <div class="stat-num" style="font-size:1.3rem;">{{ $mcqCount }}</div>
            <div class="stat-label">Published MCQs</div>
          </div>
          <a href="{{ route('teacher.mcq.create') }}" class="btn btn-sm ms-auto fw-semibold"
            style="background:#eef2ff;color:#4338ca;border-radius:8px;font-size:.72rem;padding:5px 12px;white-space:nowrap;">
            <i class="bi bi-plus-circle me-1"></i>Create MCQ
          </a>
        </div>
      </a>
    </div>
  </div>

  <div class="row g-3">
    {{-- My Subjects --}}
    <div class="col-md-4">
      <div class="section-card h-100">
        <div class="section-header">
          <h5><i class="bi bi-book me-2 text-primary"></i>Subjects I Teach</h5>
        </div>
        <div class="p-3">
          @forelse($teacher->subjects as $sub)
            <div class="d-flex align-items-center gap-2 py-2 border-bottom">
              <div style="width:8px;height:8px;border-radius:50%;background:#3730a3;flex-shrink:0;"></div>
              <span style="font-size:.875rem;font-weight:500;">{{ $sub->subject_name }}</span>
              <span class="ms-auto" style="font-size:.7rem;color:#9ca3af;">{{ $sub->subject_code ?? '' }}</span>
            </div>
          @empty
            <p class="text-muted small p-2">No subjects assigned yet.</p>
          @endforelse
        </div>
      </div>
    </div>

    {{-- Recent Submissions --}}
    <div class="col-md-8">
      <div class="section-card">
        <div class="section-header">
          <h5><i class="bi bi-journal-check me-2 text-success"></i>Recent Student Submissions</h5>
          <a href="{{ route('teacher.marks') }}" style="font-size:.78rem;color:#6366f1;">View All</a>
        </div>
        <div class="table-responsive">
          <table class="table table-hover mb-0" style="font-size:.85rem;">
            <thead style="background:#f8faff;">
              <tr>
                <th>Student</th>
                <th>MCQ Test</th>
                <th>Score</th>
                <th>%</th>
                <th>Date</th>
              </tr>
            </thead>
            <tbody>
              @forelse($results as $r)
                <tr>
                  <td class="fw-semibold">{{ $r->student->full_name ?? '—' }}</td>
                  <td>{{ $r->mcq->title ?? '—' }}</td>
                  <td>{{ $r->score }}/{{ $r->total }}</td>
                  <td>
                    @php $pct = $r->total > 0 ? round(($r->score / $r->total) * 100) : 0; @endphp
                    <span style="color:{{ $pct >= 50 ? '#16a34a' : '#dc2626' }};font-weight:700;">{{ $pct }}%</span>
                  </td>
                  <td class="text-muted">
                    {{ $r->submitted_at ? \Carbon\Carbon::parse($r->submitted_at)->format('d M') : '—' }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="5" class="text-center text-muted py-4">No submissions yet.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection
