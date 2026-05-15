@extends('portal.layout')
@section('title','Create MCQ')
@section('portal-type','Teacher Portal')
@section('sidebar-bg-inline','linear-gradient(180deg,#1e1b4b 0%,#0ea5e9 100%)')
@section('logout-route', route('teacher.logout'))
@section('user-name', session('teacher_name'))
@section('user-role','Teacher')
@section('user-class', session('teacher_class'))
@section('breadcrumb','MCQ Tests → Create')

@section('sidebar-nav')
<div class="section-label">Navigation</div>
<a href="{{ route('teacher.dashboard') }}" class="nav-link"><i class="bi bi-grid-fill"></i>Dashboard</a>
<a href="{{ route('teacher.students') }}" class="nav-link"><i class="bi bi-people-fill"></i>My Students</a>
<a href="{{ route('teacher.courses') }}" class="nav-link"><i class="bi bi-book-fill"></i>Courses</a>
<a href="{{ route('teacher.assignments') }}" class="nav-link"><i class="bi bi-file-earmark-pdf-fill"></i>Assignments</a>
<a href="{{ route('teacher.mcqs') }}" class="nav-link active"><i class="bi bi-patch-question-fill"></i>MCQ Tests</a>
<a href="{{ route('teacher.marks') }}" class="nav-link"><i class="bi bi-bar-chart-fill"></i>Results & Marks</a>
@endsection

@push('styles')
<style>
/* Time input styling */
input[type="time"]{font-family:'Inter',sans-serif;font-size:.9rem;color:#1f2937;}
input[type="time"]::-webkit-calendar-picker-indicator{cursor:pointer;opacity:.6;}
</style>
@endpush

@section('content')
<div class="section-card">
  <div class="section-header">
    <h5><i class="bi bi-plus-circle me-2 text-primary"></i>Create New MCQ Test</h5>
    <a href="{{ route('teacher.mcqs') }}" class="btn btn-sm btn-outline-secondary">← Back</a>
  </div>
  <div class="p-4">
    <form action="{{ route('teacher.mcq.store') }}" method="POST" id="mcqForm">
      @csrf
      <div class="row g-3 mb-4">

        {{-- Title --}}
        <div class="col-md-4">
          <label class="form-label fw-semibold" style="font-size:.8rem;">Test Title <span class="text-danger">*</span></label>
          <input type="text" name="title" class="form-control" required value="{{ old('title') }}" placeholder="e.g. Chapter 3 Quiz">
        </div>

        {{-- Auto class (read-only) --}}
        <div class="col-md-2">
          <label class="form-label fw-semibold" style="font-size:.8rem;">Target Class</label>
          <div class="form-control" style="background:#f1f5f9;color:#3730a3;font-weight:700;border-color:#c7d2fe;">
            <i class="bi bi-people-fill me-1" style="color:#6366f1;"></i>Class {{ session('teacher_class') }}
          </div>
        </div>

        {{-- Subject: teacher's subjects only --}}
        <div class="col-md-3">
          <label class="form-label fw-semibold" style="font-size:.8rem;">Subject</label>
          <select name="subject_id" class="form-select">
            <option value="">General / No Subject</option>
            @foreach($subjects as $sub)
            <option value="{{ $sub->id }}">{{ $sub->subject_name }}</option>
            @endforeach
          </select>
          @if($subjects->isEmpty())
          <div class="mt-1 text-muted" style="font-size:.75rem;"><i class="bi bi-info-circle me-1"></i>No subjects assigned yet.</div>
          @endif
        </div>

        {{-- Time limit: proper time input --}}
        <div class="col-md-3">
          <label class="form-label fw-semibold" style="font-size:.8rem;">
            Time Limit <small class="text-muted fw-normal">(HH:MM — when it expires)</small>
          </label>
          <div class="input-group">
            <span class="input-group-text" style="background:#f1f5f9;border-color:#e5e7eb;">
              <i class="bi bi-clock-fill text-primary"></i>
            </span>
            <input type="time" name="time_limit" class="form-control" value="{{ old('time_limit') }}"
                   placeholder="00:30" title="Set how long this MCQ is available from now">
          </div>
          <div class="mt-1 text-muted" style="font-size:.72rem;">
            <i class="bi bi-exclamation-circle me-1 text-warning"></i>After this duration from publish, no student can take/submit.
          </div>
        </div>
      </div>

      {{-- Questions --}}
      <div id="questionsContainer">
        <div class="question-block border rounded-3 p-4 mb-3" data-q="0">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0" style="color:#3730a3;">Question 1</h6>
            <button type="button" class="btn btn-sm btn-outline-danger remove-q" style="display:none;">Remove</button>
          </div>
          <div class="mb-3">
            <input type="text" name="questions[0][question]" class="form-control" placeholder="Enter your question..." required>
          </div>
          <div class="options-list">
            @foreach(['A','B','C','D'] as $l)
            <div class="row g-2 mb-2 option-row">
              <div class="col-auto d-flex align-items-center">
                <input type="radio" name="questions[0][correct]" value="{{ $loop->index }}" class="form-check-input" {{ $loop->first ? 'required' : '' }}>
              </div>
              <div class="col">
                <input type="text" name="questions[0][options][]" class="form-control form-control-sm" placeholder="Option {{ $l }}" required>
              </div>
            </div>
            @endforeach
          </div>
          <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Select the radio button next to the <strong>correct</strong> answer.</small>
        </div>
      </div>

      <div class="d-flex gap-3 mb-4">
        <button type="button" id="addQuestion" class="btn btn-outline-primary">
          <i class="bi bi-plus-circle me-2"></i>Add Question
        </button>
      </div>

      <button type="submit" class="btn fw-semibold px-5" style="background:linear-gradient(135deg,#6366f1,#3730a3);color:#fff;border-radius:12px;padding:12px 32px;">
        <i class="bi bi-check-circle me-2"></i>Publish MCQ Test
      </button>
    </form>
  </div>
</div>

@push('scripts')
<script>
let qCount = 1;
document.getElementById('addQuestion').addEventListener('click', function() {
    const idx = qCount;
    const letters = ['A','B','C','D'];
    const block = document.createElement('div');
    block.className = 'question-block border rounded-3 p-4 mb-3';
    block.dataset.q = idx;
    block.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0" style="color:#3730a3;">Question ${idx+1}</h6>
            <button type="button" class="btn btn-sm btn-outline-danger remove-q">Remove</button>
        </div>
        <div class="mb-3">
            <input type="text" name="questions[${idx}][question]" class="form-control" placeholder="Enter your question..." required>
        </div>
        <div class="options-list">
            ${letters.map((l,j) => `
            <div class="row g-2 mb-2 option-row">
                <div class="col-auto d-flex align-items-center">
                    <input type="radio" name="questions[${idx}][correct]" value="${j}" class="form-check-input" ${j===0?'required':''}>
                </div>
                <div class="col">
                    <input type="text" name="questions[${idx}][options][]" class="form-control form-control-sm" placeholder="Option ${l}" required>
                </div>
            </div>`).join('')}
        </div>
        <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Select the radio button next to the <strong>correct</strong> answer.</small>
    `;
    document.getElementById('questionsContainer').appendChild(block);
    block.querySelector('.remove-q').addEventListener('click', () => { block.remove(); updateLabels(); });
    document.querySelectorAll('.remove-q').forEach(b => b.style.display = '');
    qCount++;
});

function updateLabels() {
    document.querySelectorAll('.question-block').forEach((b, i) => {
        b.querySelector('h6').textContent = `Question ${i+1}`;
    });
    if (document.querySelectorAll('.question-block').length <= 1) {
        document.querySelectorAll('.remove-q').forEach(b => b.style.display = 'none');
    }
}
</script>
@endpush
@endsection
