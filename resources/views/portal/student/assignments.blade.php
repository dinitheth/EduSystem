@extends('portal.layout')
@section('title','Assignments')
@section('portal-type','Student Portal')
@section('sidebar-bg-inline','linear-gradient(180deg,#0f4c75 0%,#0d7377 100%)')
@section('logout-route', route('student.logout'))
@section('user-name', session('student_name'))
@section('user-role','Student')
@section('user-class', session('student_class'))
@section('breadcrumb','Assignments')

@section('sidebar-nav')
<div class="section-label">Navigation</div>
<a href="{{ route('student.dashboard') }}"   class="nav-link"><i class="bi bi-grid-fill"></i>Dashboard</a>
<a href="{{ route('student.courses') }}"     class="nav-link"><i class="bi bi-book-fill"></i>Courses</a>
<a href="{{ route('student.assignments') }}" class="nav-link active"><i class="bi bi-file-earmark-pdf-fill"></i>Assignments</a>
<a href="{{ route('student.mcqs') }}"        class="nav-link"><i class="bi bi-patch-question-fill"></i>MCQ Tests</a>
<a href="{{ route('student.marks') }}"       class="nav-link"><i class="bi bi-bar-chart-fill"></i>My Marks</a>
@endsection

@push('styles')
<style>
.submit-area{margin-top:14px;padding-top:14px;border-top:1px solid #f1f5f9;}
.file-drop{border:2px dashed #e5e7eb;border-radius:10px;padding:14px;text-align:center;cursor:pointer;transition:border .15s;}
.file-drop:hover{border-color:#6366f1;}
.done-badge{background:#dcfce7;color:#16a34a;padding:4px 12px;border-radius:20px;font-size:.72rem;font-weight:700;display:inline-flex;align-items:center;gap:5px;}
</style>
@endpush

@section('content')
<h5 class="fw-bold mb-3">Assignments for Class {{ session('student_class') }}</h5>
<div class="row g-3">
  @forelse($assignments as $a)
  @php $done = in_array($a->id, $submitted); @endphp
  <div class="col-md-6 col-lg-4">
    <div class="section-card h-100">
      <div class="p-4">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-start mb-2">
          <span class="badge-class">Class {{ $a->class }}</span>
          <div class="d-flex align-items-center gap-2">
            @if($done)
              <span class="done-badge"><i class="bi bi-check-circle-fill"></i>Submitted</span>
            @endif
            @if($a->due_date)<span style="font-size:.72rem;color:#dc2626;"><i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($a->due_date)->format('d M Y') }}</span>@endif
          </div>
        </div>
        <h6 class="fw-bold mt-2">{{ $a->title }}</h6>
        <p class="text-muted" style="font-size:.8rem;">
          {{ $a->subject->subject_name ?? 'General' }} &nbsp;·&nbsp; {{ $a->teacher->full_name ?? '' }}
        </p>
        @if($a->description)
          <p style="font-size:.8rem;color:#4b5563;">{{ Str::limit($a->description,100) }}</p>
        @endif

        {{-- Download PDF --}}
        @if($a->file_path)
        <a href="{{ Storage::url($a->file_path) }}" target="_blank" class="btn btn-sm w-100 mt-1" style="background:#fef9c3;color:#92400e;border-radius:8px;font-size:.78rem;font-weight:600;">
          <i class="bi bi-file-earmark-pdf me-1"></i>Download Assignment PDF
        </a>
        @endif

        {{-- Submit form (collapsed if already submitted) --}}
        @if(!$done)
        <div class="submit-area">
          <button class="btn btn-sm w-100 fw-semibold" onclick="toggleForm({{ $a->id }})"
            style="background:#eef2ff;color:#4338ca;border-radius:8px;font-size:.78rem;">
            <i class="bi bi-upload me-1"></i>Submit My Work
          </button>
          <div id="form-{{ $a->id }}" style="display:none;margin-top:10px;">
            <form action="{{ route('student.assignment.submit', $a->id) }}" method="POST" enctype="multipart/form-data">
              @csrf
              <div class="mb-2">
                <textarea name="notes" rows="2" placeholder="Add a note (optional)…"
                  style="width:100%;border:1.5px solid #e5e7eb;border-radius:8px;padding:7px 10px;font-size:.8rem;resize:none;font-family:'Inter',sans-serif;"></textarea>
              </div>
              <div class="mb-2">
                <input type="file" name="file" style="font-size:.78rem;width:100%;">
                <small class="text-muted">Optional: attach your work file (PDF, Word, image, etc.)</small>
              </div>
              <button type="submit" class="btn btn-sm fw-semibold w-100" style="background:linear-gradient(135deg,#0f4c75,#0d7377);color:#fff;border-radius:8px;font-size:.8rem;padding:9px;">
                <i class="bi bi-send me-1"></i>Submit Assignment
              </button>
            </form>
          </div>
        </div>
        @else
        <div class="submit-area text-center">
          <span class="done-badge" style="font-size:.78rem;padding:6px 16px;">
            <i class="bi bi-check-circle-fill"></i>Assignment Submitted
          </span>
          <p style="font-size:.7rem;color:#9ca3af;margin-top:4px;">Your teacher will grade this soon.</p>
        </div>
        @endif
      </div>
    </div>
  </div>
  @empty
  <div class="col-12">
    <div class="section-card p-4 text-center text-muted">
      <i class="bi bi-inbox" style="font-size:2rem;"></i>
      <p class="mt-2">No assignments posted for your class yet.</p>
    </div>
  </div>
  @endforelse
</div>

@push('scripts')
<script>
function toggleForm(id) {
  const f = document.getElementById('form-'+id);
  f.style.display = f.style.display === 'none' ? '' : 'none';
}
</script>
@endpush
@endsection
