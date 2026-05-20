@extends('portal.layout')
@section('title','Create MCQ')
@section('portal-type','Teacher Portal')
@section('sidebar-bg-inline','linear-gradient(180deg,#1e1b4b 0%,#0ea5e9 100%)')
@section('logout-route', route('teacher.logout'))
@section('user-name', session('teacher_name'))
@section('user-role','Teacher')
@section('user-class', session('teacher_class'))
@section('breadcrumb','MCQ Tests -> Create')

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
input[type="datetime-local"],select,input[type="number"]{font-family:'Inter',sans-serif;font-size:.9rem;color:#1f2937;}
input[type="datetime-local"]::-webkit-calendar-picker-indicator{cursor:pointer;opacity:.6;}
.import-panel{background:#f8faff;border:1px solid #e0e7ff;border-radius:12px;padding:14px 16px;margin-bottom:20px;}
.import-panel label{font-size:.8rem;font-weight:700;color:#3730a3;}
.import-status{font-size:.78rem;margin-top:8px;display:none;}
.import-status.ok{color:#15803d;display:block;}
.import-status.err{color:#b91c1c;display:block;}
.duration-chip-note{font-size:.72rem;color:#64748b;}
</style>
@endpush

@section('content')
<div class="section-card">
  <div class="section-header">
    <h5><i class="bi bi-plus-circle me-2 text-primary"></i>Create New MCQ Test</h5>
    <a href="{{ route('teacher.mcqs') }}" class="btn btn-sm btn-outline-secondary">Back</a>
  </div>
  <div class="p-4">
    <form action="{{ route('teacher.mcq.store') }}" method="POST" id="mcqForm">
      @csrf
      <div class="import-panel">
        <div class="row g-2 align-items-end">
          <div class="col-md-8">
            <label for="mcqImportFile" class="form-label mb-1"><i class="bi bi-file-earmark-arrow-up me-1"></i>Import questions from PDF, DOCX, XLSX, or XLS</label>
            <input type="file" id="mcqImportFile" class="form-control form-control-sm" accept=".pdf,.docx,.xlsx,.xls">
            <div class="text-muted mt-1" style="font-size:.72rem;">Only questions and options are imported. Correct answers are not selected automatically.</div>
          </div>
          <div class="col-md-4">
            <button type="button" id="importQuestionsBtn" class="btn btn-sm w-100 fw-semibold" style="background:#eef2ff;color:#4338ca;border-radius:9px;padding:8px 12px;">
              <i class="bi bi-magic me-1"></i>Import to Fields
            </button>
          </div>
        </div>
        <div id="importStatus" class="import-status"></div>
      </div>

      <div class="row g-3 mb-4 align-items-start">
        <div class="col-lg-3 col-md-6">
          <label class="form-label fw-semibold" style="font-size:.8rem;">Test Title <span class="text-danger">*</span></label>
          <input type="text" name="title" class="form-control" required value="{{ old('title') }}" placeholder="e.g. Chapter 3 Quiz">
          <div class="mt-1 text-muted" style="font-size:.72rem;">
            <i class="bi bi-people-fill me-1 text-primary"></i>This quiz will be published for Class {{ session('teacher_class') }}.
          </div>
        </div>

        <div class="col-lg-3 col-md-6">
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

        <div class="col-lg-3 col-md-6">
          <label class="form-label fw-semibold" style="font-size:.8rem;">Quiz Start Time</label>
          <div class="input-group">
            <span class="input-group-text" style="background:#f1f5f9;border-color:#e5e7eb;">
              <i class="bi bi-calendar-event text-primary"></i>
            </span>
            <input type="datetime-local" name="starts_at" class="form-control" value="{{ old('starts_at', now()->format('Y-m-d\\TH:i')) }}">
          </div>
          <div class="mt-1 text-muted" style="font-size:.72rem;">
            <i class="bi bi-info-circle me-1 text-primary"></i>Students can open the quiz only after this time.
          </div>
        </div>

        <div class="col-lg-3 col-md-6">
          <label class="form-label fw-semibold" style="font-size:.8rem;">Time Limit</label>
          <div class="input-group">
            <span class="input-group-text" style="background:#f1f5f9;border-color:#e5e7eb;">
              <i class="bi bi-clock-fill text-primary"></i>
            </span>
            @php($selectedTime = (string) old('time_limit', '10'))
            <select name="time_limit" id="timeLimitSelect" class="form-select">
              <option value="">No Limit</option>
              @foreach([5,10,15,20,30,45,60,90,120] as $minutes)
              <option value="{{ $minutes }}" {{ $selectedTime === (string) $minutes ? 'selected' : '' }}>{{ $minutes }} Min</option>
              @endforeach
              <option value="-1" {{ $selectedTime === '-1' ? 'selected' : '' }}>Custom</option>
            </select>
          </div>
          <div class="mt-2" id="customTimeWrap" style="display:none;">
            <div class="input-group">
              <input type="number" min="1" max="1440" name="custom_time_limit" id="customTimeLimit" class="form-control" value="{{ old('custom_time_limit') }}" placeholder="Enter minutes">
              <span class="input-group-text">Min</span>
            </div>
          </div>
          <div class="mt-1 duration-chip-note">
            <i class="bi bi-exclamation-circle me-1 text-warning"></i>Choose a ready-made duration like 5 Min, 10 Min, 20 Min, or set your own.
          </div>
        </div>
      </div>

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
const timeLimitSelect = document.getElementById('timeLimitSelect');
const customTimeWrap = document.getElementById('customTimeWrap');
const customTimeLimit = document.getElementById('customTimeLimit');

function syncCustomTimeVisibility() {
    const useCustom = timeLimitSelect.value === '-1';
    customTimeWrap.style.display = useCustom ? 'block' : 'none';
    customTimeLimit.required = useCustom;
    if (!useCustom) customTimeLimit.value = '';
}

timeLimitSelect.addEventListener('change', syncCustomTimeVisibility);
syncCustomTimeVisibility();

document.getElementById('addQuestion').addEventListener('click', function() {
    addQuestionBlock();
});

function addQuestionBlock(question = '', options = []) {
    const idx = qCount;
    const letters = ['A','B','C','D'];
    const normalizedOptions = [...options];
    while (normalizedOptions.length < 4) normalizedOptions.push('');
    const block = document.createElement('div');
    block.className = 'question-block border rounded-3 p-4 mb-3';
    block.dataset.q = idx;
    block.innerHTML = `
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0" style="color:#3730a3;">Question ${idx+1}</h6>
            <button type="button" class="btn btn-sm btn-outline-danger remove-q">Remove</button>
        </div>
        <div class="mb-3">
            <input type="text" name="questions[${idx}][question]" class="form-control" placeholder="Enter your question..." required value="${escAttr(question)}">
        </div>
        <div class="options-list">
            ${normalizedOptions.map((option,j) => `
            <div class="row g-2 mb-2 option-row">
                <div class="col-auto d-flex align-items-center">
                    <input type="radio" name="questions[${idx}][correct]" value="${j}" class="form-check-input" ${j===0?'required':''}>
                </div>
                <div class="col">
                    <input type="text" name="questions[${idx}][options][]" class="form-control form-control-sm" placeholder="Option ${letters[j] || (j+1)}" required value="${escAttr(option)}">
                </div>
            </div>`).join('')}
        </div>
        <small class="text-muted"><i class="bi bi-info-circle me-1"></i>Select the radio button next to the <strong>correct</strong> answer.</small>
    `;
    document.getElementById('questionsContainer').appendChild(block);
    block.querySelector('.remove-q').addEventListener('click', () => { block.remove(); updateLabels(); });
    document.querySelectorAll('.remove-q').forEach(b => b.style.display = '');
    qCount++;
}

function updateLabels() {
    document.querySelectorAll('.question-block').forEach((b, i) => {
        b.querySelector('h6').textContent = `Question ${i+1}`;
    });
    if (document.querySelectorAll('.question-block').length <= 1) {
        document.querySelectorAll('.remove-q').forEach(b => b.style.display = 'none');
    }
}

function escAttr(value) {
  return String(value || '').replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
}

function resetQuestions(questions) {
  const container = document.getElementById('questionsContainer');
  container.innerHTML = '';
  qCount = 0;
  questions.forEach(item => addQuestionBlock(item.question, item.options || []));
  updateLabels();
}

document.getElementById('importQuestionsBtn').addEventListener('click', async function() {
  const fileInput = document.getElementById('mcqImportFile');
  const status = document.getElementById('importStatus');
  const btn = this;

  status.className = 'import-status';
  status.textContent = '';

  if (!fileInput.files.length) {
    status.className = 'import-status err';
    status.textContent = 'Choose a PDF, DOCX, XLSX, or XLS file first.';
    return;
  }

  const fd = new FormData();
  fd.append('file', fileInput.files[0]);
  btn.disabled = true;
  btn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Importing...';

  try {
    const res = await fetch("{{ route('teacher.mcq.import') }}", {
      method: 'POST',
      headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest'},
      body: fd
    });
    const data = await res.json();
    if (!res.ok) throw new Error(data.message || 'Import failed.');
    resetQuestions(data.questions);
    status.className = 'import-status ok';
    status.textContent = data.message;
  } catch (err) {
    status.className = 'import-status err';
    status.textContent = err.message;
  } finally {
    btn.disabled = false;
    btn.innerHTML = '<i class="bi bi-magic me-1"></i>Import to Fields';
  }
});
</script>
@endpush
@endsection
