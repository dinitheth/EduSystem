@extends('portal.layout')
@section('title','Assignments')
@section('portal-type','Teacher Portal')
@section('sidebar-bg-inline','linear-gradient(180deg,#1e1b4b 0%,#0ea5e9 100%)')
@section('logout-route', route('teacher.logout'))
@section('user-name', session('teacher_name'))
@section('user-role','Teacher')
@section('user-class', session('teacher_class'))
@section('breadcrumb','Assignments')

@section('sidebar-nav')
<div class="section-label">Navigation</div>
<a href="{{ route('teacher.dashboard') }}"   class="nav-link"><i class="bi bi-grid-fill"></i>Dashboard</a>
<a href="{{ route('teacher.students') }}"    class="nav-link"><i class="bi bi-people-fill"></i>My Students</a>
<a href="{{ route('teacher.courses') }}"     class="nav-link"><i class="bi bi-book-fill"></i>Courses</a>
<a href="{{ route('teacher.assignments') }}" class="nav-link active"><i class="bi bi-file-earmark-pdf-fill"></i>Assignments</a>
<a href="{{ route('teacher.mcqs') }}"        class="nav-link"><i class="bi bi-patch-question-fill"></i>MCQ Tests</a>
<a href="{{ route('teacher.marks') }}"       class="nav-link"><i class="bi bi-bar-chart-fill"></i>Results & Marks</a>
@endsection

@section('content')
<div class="row g-3">
  {{-- Post Assignment Form --}}
  <div class="col-md-4">
    <div class="section-card">
      <div class="section-header"><h5><i class="bi bi-upload me-2"></i>Post Assignment</h5></div>
      <div class="p-4">
        <form action="{{ route('teacher.assignments.store') }}" method="POST" enctype="multipart/form-data">
          @csrf

          {{-- Auto class badge --}}
          <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:.8rem;">Target Class</label>
            <div class="d-flex align-items-center gap-2 p-2 rounded-3" style="background:#eef2ff;border:1.5px solid #c7d2fe;">
              <i class="bi bi-people-fill" style="color:#6366f1;"></i>
              <span class="fw-bold" style="color:#3730a3;">Class {{ session('teacher_class') }}</span>
              <small class="text-muted ms-1">(your assigned class)</small>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:.8rem;">Title <span class="text-danger">*</span></label>
            <input type="text" name="title" class="form-control form-control-sm" placeholder="e.g. Chapter 5 Worksheet" required value="{{ old('title') }}">
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:.8rem;">Subject</label>
            <select name="subject_id" class="form-select form-select-sm">
              <option value="">General / No Subject</option>
              @foreach($subjects as $sub)
              <option value="{{ $sub->id }}">{{ $sub->subject_name }}</option>
              @endforeach
            </select>
            @if($subjects->isEmpty())
            <div class="mt-1 text-muted" style="font-size:.75rem;"><i class="bi bi-info-circle me-1"></i>No subjects assigned to you yet.</div>
            @endif
          </div>

          <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:.8rem;">Description</label>
            <textarea name="description" class="form-control form-control-sm" rows="3" placeholder="Assignment details...">{{ old('description') }}</textarea>
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:.8rem;">Due Date</label>
            <input type="date" name="due_date" class="form-control form-control-sm" value="{{ old('due_date') }}">
          </div>
          <div class="mb-3">
            <label class="form-label fw-semibold" style="font-size:.8rem;">PDF File</label>
            <input type="file" name="file" class="form-control form-control-sm" accept=".pdf">
          </div>
          <button type="submit" class="btn btn-sm fw-semibold w-100" style="background:linear-gradient(135deg,#f59e0b,#d97706);color:#fff;border-radius:10px;padding:10px;">
            <i class="bi bi-upload me-2"></i>Post Assignment
          </button>
        </form>
      </div>
    </div>
  </div>

  {{-- Assignment List --}}
  <div class="col-md-8">
    <div class="section-card">
      <div class="section-header"><h5><i class="bi bi-file-earmark-pdf me-2 text-warning"></i>Posted Assignments ({{ $assignments->count() }})</h5></div>
      <div class="table-responsive">
        <table class="table table-hover mb-0" style="font-size:.85rem;">
          <thead style="background:#f8faff;">
            <tr><th>Title</th><th>Class</th><th>Subject</th><th>Due</th><th>Submissions</th><th>File</th><th>Actions</th></tr>
          </thead>
          <tbody>
            @forelse($assignments as $a)
            <tr>
              <td><div class="fw-semibold">{{ $a->title }}</div><div class="text-muted" style="font-size:.72rem;">{{ Str::limit($a->description,60) }}</div></td>
              <td><span class="badge-class">{{ $a->class }}</span></td>
              <td>{{ $a->subject->subject_name ?? 'General' }}</td>
              <td>{{ $a->due_date ? \Carbon\Carbon::parse($a->due_date)->format('d M Y') : '—' }}</td>
              <td>
                <a href="{{ route('teacher.assignment.submissions', $a->id) }}" class="btn btn-sm" style="background:#eef2ff;color:#6366f1;font-size:.72rem;border-radius:8px;padding:4px 10px;font-weight:600;">
                  <i class="bi bi-inbox me-1"></i>{{ $a->submissions_count }}
                </a>
              </td>
              <td>
                @if($a->file_path)
                <a href="{{ Storage::url($a->file_path) }}" target="_blank" class="btn btn-sm" style="background:#eef2ff;color:#6366f1;font-size:.72rem;"><i class="bi bi-download me-1"></i>PDF</a>
                @else <span class="text-muted">—</span> @endif
              </td>
              <td>
                <a href="{{ route('teacher.assignment.submissions', $a->id) }}" class="btn btn-sm fw-semibold" style="background:#f0fdf4;color:#16a34a;border-radius:8px;font-size:.72rem;padding:4px 12px;">
                  <i class="bi bi-pencil-square me-1"></i>Grade
                </a>
              </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center text-muted py-4">No assignments posted yet.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
