@extends('portal.layout')
@section('title','Take MCQ')
@section('portal-type','Student Portal')
@section('sidebar-bg-inline','linear-gradient(180deg,#0f4c75 0%,#0d7377 100%)')
@section('logout-route', route('student.logout'))
@section('user-name', session('student_name'))
@section('user-role','Student')
@section('user-class', session('student_class'))
@section('breadcrumb','MCQ Tests → Take Test')

@section('sidebar-nav')
<div class="section-label">Navigation</div>
<a href="{{ route('student.dashboard') }}" class="nav-link"><i class="bi bi-grid-fill"></i>Dashboard</a>
<a href="{{ route('student.courses') }}" class="nav-link"><i class="bi bi-book-fill"></i>Courses</a>
<a href="{{ route('student.assignments') }}" class="nav-link"><i class="bi bi-file-earmark-pdf-fill"></i>Assignments</a>
<a href="{{ route('student.mcqs') }}" class="nav-link active"><i class="bi bi-patch-question-fill"></i>MCQ Tests</a>
<a href="{{ route('student.marks') }}" class="nav-link"><i class="bi bi-bar-chart-fill"></i>My Marks</a>
@endsection

@section('content')
<div class="section-card mb-3">
  <div class="section-header">
    <div>
      <h5 class="fw-bold mb-0">{{ $mcq->title }}</h5>
      <small class="text-muted">{{ $mcq->subject->subject_name ?? 'General' }} &nbsp;·&nbsp; {{ $mcq->questions->count() }} Questions @if($mcq->time_limit)&nbsp;·&nbsp; <span id="timer" style="color:#dc2626;font-weight:700;">{{ $mcq->time_limit }}:00</span>@endif</small>
    </div>
    <span class="badge-class">Class {{ $mcq->class }}</span>
  </div>
</div>

<form action="{{ route('student.mcq.submit', $mcq->id) }}" method="POST" id="mcqForm">
  @csrf
  @foreach($mcq->questions as $i => $q)
  <div class="section-card mb-3">
    <div class="p-4">
      <div class="fw-semibold mb-3" style="font-size:.95rem;color:#1f2937;">
        <span style="color:#6366f1;font-weight:800;">Q{{ $i+1 }}.</span> {{ $q->question }}
      </div>
      <div class="row g-2">
        @foreach($q->options as $j => $opt)
        <div class="col-md-6">
          <label class="option-card d-flex align-items-center gap-3 p-3 border rounded-3 cursor-pointer" style="cursor:pointer;transition:background .15s;background:#f8faff;" onclick="this.style.background='#eef2ff';this.style.borderColor='#6366f1';">
            <input type="radio" name="answers[{{ $q->id }}]" value="{{ $opt->id }}" class="form-check-input flex-shrink-0" required>
            <span style="font-size:.875rem;">{{ $opt->option_text }}</span>
          </label>
        </div>
        @endforeach
      </div>
    </div>
  </div>
  @endforeach

  <div class="d-flex gap-3">
    <button type="submit" class="btn fw-semibold px-5" style="background:linear-gradient(135deg,#0f4c75,#0d7377);color:#fff;border-radius:12px;padding:12px 32px;" onclick="return confirm('Submit your answers? You cannot change them after submission.')">
      <i class="bi bi-check-circle me-2"></i>Submit Answers
    </button>
    <a href="{{ route('student.mcqs') }}" class="btn btn-outline-secondary" style="border-radius:12px;padding:12px 24px;">Cancel</a>
  </div>
</form>

@if($mcq->time_limit)
@push('scripts')
<script>
let total = {{ $mcq->time_limit * 60 }};
const timerEl = document.getElementById('timer');
const iv = setInterval(() => {
    total--;
    const m = Math.floor(total/60), s = total%60;
    timerEl.textContent = `${m}:${s<10?'0':''}${s}`;
    if (total <= 0) { clearInterval(iv); document.getElementById('mcqForm').submit(); }
}, 1000);
</script>
@endpush
@endif
@endsection
