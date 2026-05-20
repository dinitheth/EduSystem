@extends('portal.layout')
@section('title','MCQ Tests')
@section('portal-type','Student Portal')
@section('sidebar-bg-inline','linear-gradient(180deg,#0f4c75 0%,#0d7377 100%)')
@section('logout-route', route('student.logout'))
@section('user-name', session('student_name'))
@section('user-role','Student')
@section('user-class', session('student_class'))
@section('breadcrumb','MCQ Tests')

@section('sidebar-nav')
<div class="section-label">Navigation</div>
<a href="{{ route('student.dashboard') }}" class="nav-link"><i class="bi bi-grid-fill"></i>Dashboard</a>
<a href="{{ route('student.courses') }}" class="nav-link"><i class="bi bi-book-fill"></i>Courses</a>
<a href="{{ route('student.assignments') }}" class="nav-link"><i class="bi bi-file-earmark-pdf-fill"></i>Assignments</a>
<a href="{{ route('student.mcqs') }}" class="nav-link active"><i class="bi bi-patch-question-fill"></i>MCQ Tests</a>
<a href="{{ route('student.marks') }}" class="nav-link"><i class="bi bi-bar-chart-fill"></i>My Marks</a>
@endsection

@section('content')
<h5 class="fw-bold mb-3">MCQ Tests for Class {{ session('student_class') }}</h5>
<div class="row g-3">
  @forelse($mcqs as $m)
  @php
    $submitted = in_array($m->id, $done);
    $submission = $submissions[$m->id] ?? null;
    $startsAt = $m->starts_at;
    $isUpcoming = $startsAt && now()->lt($startsAt);
    $isExpired = $m->expires_at && now()->isAfter($m->expires_at);
    $questionCnt = $m->questions()->count();
    $durationLabel = $m->time_limit
      ? ($m->time_limit >= 60
        ? floor($m->time_limit / 60) . ' hr' . (floor($m->time_limit / 60) > 1 ? 's' : '') . (($m->time_limit % 60) ? ' ' . ($m->time_limit % 60) . ' min' : '')
        : $m->time_limit . ' min')
      : null;
    $timeStr = $durationLabel ? ' · ' . $durationLabel : '';
    $cardBorder = $isExpired ? '#9ca3af' : ($submitted ? '#16a34a' : ($isUpcoming ? '#f59e0b' : '#6366f1'));
    $btnBg = $submitted ? '#d1fae5' : (($isExpired || $isUpcoming) ? '#f1f5f9' : 'linear-gradient(135deg,#6366f1,#3730a3)');
    $btnColor = $submitted ? '#065f46' : (($isExpired || $isUpcoming) ? '#9ca3af' : '#fff');
  @endphp
  <div class="col-md-6 col-lg-4">
    <div class="section-card h-100" style="border-top:4px solid {{ $cardBorder }};">
      <div class="p-4">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <span class="badge-class">Class {{ $m->class }}</span>
          @if($isExpired && !$submitted)
            <span style="background:#fee2e2;color:#991b1b;padding:3px 10px;border-radius:20px;font-size:.7rem;font-weight:700;">Expired</span>
          @elseif($submitted)
            <span style="background:#d1fae5;color:#065f46;padding:3px 10px;border-radius:20px;font-size:.7rem;font-weight:700;">Completed</span>
          @elseif($isUpcoming)
            <span style="background:#fef3c7;color:#92400e;padding:3px 10px;border-radius:20px;font-size:.7rem;font-weight:700;">Starts Soon</span>
          @else
            <span style="background:#eef2ff;color:#6366f1;padding:3px 10px;border-radius:20px;font-size:.7rem;font-weight:700;">Pending</span>
          @endif
        </div>

        <h6 class="fw-bold mt-2">{{ $m->title }}</h6>
        <p class="text-muted" style="font-size:.8rem;">
          {{ $m->subject->subject_name ?? 'General' }} · {{ $questionCnt }} Questions{{ $timeStr }}
        </p>
        <p style="font-size:.78rem;color:#6b7280;">By: {{ $m->teacher->full_name ?? '' }}</p>

        @if($startsAt)
        <p style="font-size:.72rem;color:{{ $isUpcoming ? '#d97706' : '#0f766e' }};">
          <i class="bi bi-calendar-event me-1"></i>
          Start Time: {{ $startsAt->format('d M Y · h:i A') }}
        </p>
        @endif

        @if($m->expires_at)
        <p style="font-size:.72rem;color:{{ $isExpired ? '#dc2626' : '#f59e0b' }};">
          <i class="bi bi-clock me-1"></i>
          @if($isExpired)
            Expired {{ \Carbon\Carbon::parse($m->expires_at)->diffForHumans() }}
          @else
            Closes {{ \Carbon\Carbon::parse($m->expires_at)->diffForHumans() }}
          @endif
        </p>
        @endif

        @if(($isExpired || $isUpcoming) && !$submitted)
          <div class="btn btn-sm w-100 mt-2 fw-semibold" style="background:#f1f5f9;color:#9ca3af;border-radius:8px;font-size:.85rem;padding:8px;cursor:not-allowed;">
            <i class="bi bi-lock me-1"></i>{{ $isUpcoming ? 'Not Started Yet' : 'Test Closed' }}
          </div>
        @else
          <a href="{{ $submitted && $submission ? route('student.mcq.result', $submission->id) : route('student.mcq.take', $m->id) }}"
             class="btn btn-sm w-100 mt-2 fw-semibold"
             style="background:{{ $btnBg }};color:{{ $btnColor }};border-radius:8px;font-size:.85rem;padding:8px;">
            @if($submitted)
              <i class="bi bi-eye me-1"></i>View Result
            @else
              <i class="bi bi-play-fill me-1"></i>Start Test
            @endif
          </a>
        @endif
      </div>
    </div>
  </div>
  @empty
  <div class="col-12">
    <div class="section-card p-4 text-center text-muted">
      <i class="bi bi-patch-question" style="font-size:2rem;"></i>
      <p class="mt-2">No MCQ tests available for your class yet.</p>
    </div>
  </div>
  @endforelse
</div>
@endsection
