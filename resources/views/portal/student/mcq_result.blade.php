@extends('portal.layout')
@section('title','MCQ Result')
@section('portal-type','Student Portal')
@section('sidebar-bg-inline','linear-gradient(180deg,#0f4c75 0%,#0d7377 100%)')
@section('logout-route', route('student.logout'))
@section('user-name', session('student_name'))
@section('user-role','Student')
@section('user-class', session('student_class'))
@section('breadcrumb','MCQ Tests → Result')

@section('sidebar-nav')
<div class="section-label">Navigation</div>
<a href="{{ route('student.dashboard') }}" class="nav-link"><i class="bi bi-grid-fill"></i>Dashboard</a>
<a href="{{ route('student.courses') }}" class="nav-link"><i class="bi bi-book-fill"></i>Courses</a>
<a href="{{ route('student.assignments') }}" class="nav-link"><i class="bi bi-file-earmark-pdf-fill"></i>Assignments</a>
<a href="{{ route('student.mcqs') }}" class="nav-link active"><i class="bi bi-patch-question-fill"></i>MCQ Tests</a>
<a href="{{ route('student.marks') }}" class="nav-link"><i class="bi bi-bar-chart-fill"></i>My Marks</a>
@endsection

@section('content')
@php
    $pct   = $submission->total > 0 ? round(($submission->score / $submission->total) * 100) : 0;
    $grade = $pct >= 80 ? 'A' : ($pct >= 60 ? 'B' : ($pct >= 40 ? 'C' : 'F'));
    $color = $pct >= 60 ? '#16a34a' : ($pct >= 40 ? '#f59e0b' : '#dc2626');
    $answersMap = $submission->answers->keyBy('question_id');
@endphp

{{-- Score Card --}}
<div class="hello-banner mb-4" style="background:linear-gradient(135deg,{{ $pct>=60?'#0f4c75, #0d7377':'#7f1d1d, #dc2626' }});">
  <div class="d-flex align-items-center gap-4">
    <div style="width:96px;height:96px;border-radius:50%;background:rgba(255,255,255,.15);display:flex;flex-direction:column;align-items:center;justify-content:center;">
      <div style="font-size:1.8rem;font-weight:900;line-height:1;">{{ $pct }}%</div>
      <div style="font-size:.75rem;opacity:.8;">Score</div>
    </div>
    <div>
      <h2 style="font-size:1.5rem;">{{ $submission->score }} / {{ $submission->total }} Correct</h2>
      <p style="opacity:.85;">Grade: <strong>{{ $grade }}</strong> &nbsp;·&nbsp; {{ $submission->mcq->title }}</p>
      <p style="opacity:.75;font-size:.8rem;">Submitted: {{ $submission->submitted_at ? \Carbon\Carbon::parse($submission->submitted_at)->format('d M Y, H:i') : '' }}</p>
    </div>
  </div>
</div>

{{-- Question Review --}}
@foreach($submission->mcq->questions as $i => $q)
@php
    $answer     = $answersMap[$q->id] ?? null;
    $isCorrect  = $answer && $answer->is_correct;
    $correctOpt = $q->options->where('is_correct', true)->first();
    $chosenOpt  = $answer ? $q->options->where('id', $answer->option_id)->first() : null;
@endphp
<div class="section-card mb-3" style="border-left:4px solid {{ $isCorrect ? '#16a34a' : '#dc2626' }};">
  <div class="p-4">
    <div class="d-flex align-items-start gap-3 mb-3">
      <span style="background:{{ $isCorrect?'#d1fae5':'#fee2e2' }};color:{{ $isCorrect?'#065f46':'#991b1b' }};padding:4px 10px;border-radius:20px;font-size:.72rem;font-weight:700;flex-shrink:0;">
        {{ $isCorrect ? '✓ Correct' : '✗ Wrong' }}
      </span>
      <span class="fw-semibold" style="font-size:.95rem;"><span style="color:#6366f1;font-weight:800;">Q{{ $i+1 }}.</span> {{ $q->question }}</span>
    </div>
    <div class="row g-2">
      @foreach($q->options as $opt)
      @php
        $isChosen  = $chosenOpt && $chosenOpt->id === $opt->id;
        $isCorrectOpt = $opt->is_correct;
        $bg = $isCorrectOpt ? '#d1fae5' : ($isChosen && !$isCorrectOpt ? '#fee2e2' : '#f8faff');
        $border = $isCorrectOpt ? '#16a34a' : ($isChosen && !$isCorrectOpt ? '#dc2626' : '#e5e7eb');
      @endphp
      <div class="col-md-6">
        <div class="p-3 rounded-3 d-flex align-items-center gap-2" style="background:{{ $bg }};border:1.5px solid {{ $border }};">
          @if($isCorrectOpt)<i class="bi bi-check-circle-fill" style="color:#16a34a;"></i>
          @elseif($isChosen)<i class="bi bi-x-circle-fill" style="color:#dc2626;"></i>
          @else<i class="bi bi-circle" style="color:#d1d5db;"></i>@endif
          <span style="font-size:.875rem;">{{ $opt->option_text }}</span>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</div>
@endforeach

<div class="mt-3">
  <a href="{{ route('student.mcqs') }}" class="btn btn-outline-secondary me-2" style="border-radius:10px;"><i class="bi bi-arrow-left me-1"></i>Back to MCQs</a>
  <a href="{{ route('student.marks') }}" class="btn" style="background:linear-gradient(135deg,#0f4c75,#0d7377);color:#fff;border-radius:10px;"><i class="bi bi-bar-chart me-1"></i>View All Marks</a>
</div>
@endsection
