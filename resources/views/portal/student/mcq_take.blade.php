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
<style>
  .mcq-layout{display:grid;grid-template-columns:minmax(0,1fr) 248px;gap:20px;align-items:start;}
  .mcq-main .section-card{scroll-margin-top:92px;}
  .question-nav{position:sticky;top:18px;border-radius:20px;overflow:hidden;background:#fff;border:1px solid #dbe7f5;box-shadow:0 18px 42px rgba(15,76,117,.08);}
  .question-nav-header{padding:16px 18px;border-bottom:1px solid #e5eef8;background:linear-gradient(135deg,#f8fbff,#eef6ff);}
.question-grid{display:flex;flex-wrap:wrap;gap:4px;padding:10px 12px;}
  .question-chip{display:flex;align-items:center;justify-content:center;width:24px;height:24px;border-radius:999px;border:1px solid #d9e3ef;background:#fff;color:#334155;font-weight:700;font-size:.72rem;text-decoration:none;transition:transform .18s ease,box-shadow .18s ease,background .18s ease,color .18s ease,border-color .18s ease;}
  .question-chip:hover{transform:translateY(-2px);box-shadow:0 10px 20px rgba(15,23,42,.08);border-color:#60a5fa;color:#0f172a;}
  .question-chip.active{background:#2563eb;border-color:#2563eb;color:#fff;box-shadow:0 12px 24px rgba(37,99,235,.28);}
  .question-chip.answered{background:#dcfce7;border-color:#4ade80;color:#166534;}
  .question-chip.unanswered{background:#ffffff;border-color:#dbe4ee;color:#64748b;}
  .question-progress{padding:0 12px 12px;font-size:.72rem;color:#64748b;}
  .question-block.active-card{border:1px solid rgba(59,130,246,.35);box-shadow:0 18px 38px rgba(37,99,235,.08);}
  .option-card{position:relative;}
  .option-card.selected{background:#eaf3ff !important;border-color:#3b82f6 !important;box-shadow:0 8px 20px rgba(59,130,246,.12);}
  @media (max-width: 991.98px){
    .mcq-layout{grid-template-columns:1fr;}
    .question-nav{position:static;order:-1;}
    .question-grid{gap:4px;}
  }
  @media (max-width: 575.98px){
    .question-grid{gap:4px;padding:10px;}
    .question-chip{width:22px;height:22px;border-radius:999px;font-size:.68rem;}
  }
</style>
<div class="section-card mb-3">
  <div class="section-header">
    <div>
      <h5 class="fw-bold mb-0">{{ $mcq->title }}</h5>
      <small class="text-muted">
        {{ $mcq->subject->subject_name ?? 'General' }} &nbsp;·&nbsp; {{ $mcq->questions->count() }} Questions
        @if($mcq->time_limit)
        &nbsp;·&nbsp;
        <span id="timer" data-remaining-seconds="{{ max(0, now()->diffInSeconds($mcq->expires_at, false)) }}" style="color:#dc2626;font-weight:700;">{{ $mcq->time_limit }} Min</span>
        @endif
      </small>
    </div>
    <span class="badge-class">Class {{ $mcq->class }}</span>
  </div>
</div>

<div class="mcq-layout">
<form action="{{ route('student.mcq.submit', $mcq->id) }}" method="POST" id="mcqForm" class="mcq-main"
  data-confirm-title="Submit MCQ Answers"
  data-confirm-message="Submit your answers now? You cannot change them after submission."
  data-confirm-text="Submit Answers">
  @csrf
  @foreach($mcq->questions as $i => $q)
  <div class="section-card mb-3 question-block" id="question-{{ $i+1 }}" data-question-index="{{ $i+1 }}">
    <div class="p-4">
      <div class="fw-semibold mb-3" style="font-size:.95rem;color:#1f2937;">
        <span style="color:#6366f1;font-weight:800;">Q{{ $i+1 }}.</span> {{ $q->question }}
      </div>
      <div class="row g-2">
        @foreach($q->options as $j => $opt)
        <div class="col-md-6">
          <label class="option-card d-flex align-items-center gap-3 p-3 border rounded-3 cursor-pointer" style="cursor:pointer;transition:background .15s;background:#f8faff;">
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
    <button type="submit" class="btn fw-semibold px-5" style="background:linear-gradient(135deg,#0f4c75,#0d7377);color:#fff;border-radius:12px;padding:12px 32px;">
      <i class="bi bi-check-circle me-2"></i>Submit Answers
    </button>
    <a href="{{ route('student.mcqs') }}" class="btn btn-outline-secondary" style="border-radius:12px;padding:12px 24px;">Cancel</a>
  </div>
</form>
<aside class="question-nav">
  <div class="question-nav-header">
    <div class="fw-bold text-dark">Questions</div>
    <div class="small text-muted">Track answered and current question status while you work.</div>
  </div>
  <div class="question-grid" id="questionNavGrid">
    @foreach($mcq->questions as $i => $q)
      <a href="#question-{{ $i+1 }}" class="question-chip unanswered" data-target="question-{{ $i+1 }}" data-question-id="{{ $q->id }}">{{ $i+1 }}</a>
    @endforeach
  </div>
  <div class="question-progress"><span id="answeredCount">0</span> of {{ $mcq->questions->count() }} answered</div>
</aside>
</div>

@push('scripts')
<script>
@if($mcq->time_limit)
const timerEl = document.getElementById('timer');
let total = parseInt(timerEl.dataset.remainingSeconds || '0', 10);

function formatRemainingTime(seconds) {
    if (seconds <= 0) return '0 Min';
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const secs = seconds % 60;

    if (hours > 0) return `${hours} Hr ${minutes} Min`;
    if (minutes > 0 && secs === 0) return `${minutes} Min`;
    if (minutes > 0) return `${minutes} Min ${secs} Sec`;
    return `${secs} Sec`;
}

timerEl.textContent = formatRemainingTime(total);
const iv = setInterval(() => {
    total--;
    timerEl.textContent = formatRemainingTime(total);
    if (total <= 0) { clearInterval(iv); document.getElementById('mcqForm').submit(); }
}, 1000);
@endif

const questionBlocks = [...document.querySelectorAll('.question-block')];
const navChips = [...document.querySelectorAll('.question-chip')];
const answeredCountEl = document.getElementById('answeredCount');

function updateQuestionState() {
    let answered = 0;

    questionBlocks.forEach((block) => {
        const checked = block.querySelector('input[type="radio"]:checked');
        const index = block.dataset.questionIndex;
        const chip = navChips.find((nav) => nav.textContent.trim() === index);

        block.querySelectorAll('.option-card').forEach((card) => {
            card.classList.toggle('selected', !!card.querySelector('input:checked'));
        });

        if (!chip) return;

        chip.classList.remove('answered', 'unanswered');
        if (checked) {
            answered++;
            chip.classList.add('answered');
        } else {
            chip.classList.add('unanswered');
        }
    });

    answeredCountEl.textContent = answered;
}

function setActiveQuestion() {
    let activeBlock = questionBlocks[0];
    const topOffset = 140;

    questionBlocks.forEach((block) => {
        const rect = block.getBoundingClientRect();
        if (rect.top - topOffset <= 0) {
            activeBlock = block;
        }
        block.classList.toggle('active-card', block === activeBlock);
    });

    navChips.forEach((chip) => {
        chip.classList.toggle('active', chip.dataset.target === activeBlock.id);
    });
}

document.querySelectorAll('.option-card input[type="radio"]').forEach((input) => {
    input.addEventListener('change', updateQuestionState);
});

navChips.forEach((chip) => {
    chip.addEventListener('click', () => {
        setTimeout(setActiveQuestion, 120);
    });
});

window.addEventListener('scroll', setActiveQuestion, { passive: true });
updateQuestionState();
setActiveQuestion();
</script>
@endpush
@endsection
