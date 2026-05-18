@extends('portal.layout')
@section('title','Assignment Submissions')
@section('portal-type','Teacher Portal')
@section('sidebar-bg-inline','linear-gradient(180deg,#1e1b4b 0%,#0ea5e9 100%)')
@section('logout-route', route('teacher.logout'))
@section('user-name', session('teacher_name'))
@section('user-role','Teacher')
@section('user-class', session('teacher_class'))
@section('breadcrumb','Assignments → Submissions')

@section('sidebar-nav')
<div class="section-label">Navigation</div>
<a href="{{ route('teacher.dashboard') }}"   class="nav-link"><i class="bi bi-grid-fill"></i>Dashboard</a>
<a href="{{ route('teacher.students') }}"    class="nav-link"><i class="bi bi-people-fill"></i>My Students</a>
<a href="{{ route('teacher.courses') }}"     class="nav-link"><i class="bi bi-book-fill"></i>Courses</a>
<a href="{{ route('teacher.assignments') }}" class="nav-link active"><i class="bi bi-file-earmark-pdf-fill"></i>Assignments</a>
<a href="{{ route('teacher.mcqs') }}"        class="nav-link"><i class="bi bi-patch-question-fill"></i>MCQ Tests</a>
<a href="{{ route('teacher.marks') }}"       class="nav-link"><i class="bi bi-bar-chart-fill"></i>Results &amp; Marks</a>
@endsection

@push('styles')
<style>
.sub-card{background:#fff;border-radius:12px;border:1px solid #f1f5f9;box-shadow:0 1px 6px rgba(0,0,0,.06);padding:18px 20px;margin-bottom:14px;}
.grade-badge{display:inline-block;padding:3px 11px;border-radius:20px;font-size:.7rem;font-weight:700;}
.grade-pill-input{width:80px;border:1.5px solid #e5e7eb;border-radius:8px;padding:5px 8px;font-size:.85rem;text-align:center;font-family:'Inter',sans-serif;}
.grade-pill-input:focus{border-color:#6366f1;outline:none;}
.feedback-ta{border:1.5px solid #e5e7eb;border-radius:8px;padding:8px 10px;font-size:.82rem;width:100%;resize:vertical;font-family:'Inter',sans-serif;}
.feedback-ta:focus{border-color:#6366f1;outline:none;}
</style>
@endpush

@section('content')
<div class="d-flex align-items-center gap-3 mb-3">
  <a href="{{ route('teacher.assignments') }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Back</a>
  <h5 class="fw-bold mb-0" style="color:#1f2937;">{{ $assignment->title }}</h5>
  @if($assignment->subject)
    <span class="badge-class">{{ $assignment->subject->subject_name }}</span>
  @endif
  @if($assignment->due_date)
    <span style="font-size:.75rem;color:#dc2626;"><i class="bi bi-clock me-1"></i>Due {{ \Carbon\Carbon::parse($assignment->due_date)->format('d M Y') }}</span>
  @endif
</div>

<div class="section-card mb-3">
  <div class="section-header">
    <h5><i class="bi bi-inbox me-2 text-primary"></i>Submissions ({{ $submissions->count() }})</h5>
    <div class="d-flex gap-3 align-items-center">
      <span style="font-size:.78rem;color:#6b7280;">
        <span style="background:#dcfce7;color:#16a34a;padding:2px 8px;border-radius:20px;font-weight:700;">{{ $submissions->where('status','graded')->count() }}</span> graded
        &nbsp;
        <span style="background:#fef9c3;color:#ca8a04;padding:2px 8px;border-radius:20px;font-weight:700;">{{ $submissions->where('status','submitted')->count() }}</span> pending
      </span>
      @if($submissions->whereNotNull('file_path')->count() > 0)
      <a href="{{ route('teacher.assignment.submissions.download', $assignment->id) }}" class="btn btn-sm fw-semibold" style="background:linear-gradient(135deg,#0f4c75,#0d7377);color:#fff;border-radius:9px;font-size:.75rem;padding:7px 16px;">
        <i class="bi bi-file-zip me-1"></i>Download All (ZIP)
      </a>
      @endif
      <a href="{{ route('teacher.assignment.submissions.excel', $assignment->id) }}" class="btn btn-sm fw-semibold" style="background:linear-gradient(135deg,#16a34a,#15803d);color:#fff;border-radius:9px;font-size:.75rem;padding:7px 16px;">
        <i class="bi bi-file-earmark-excel me-1"></i>Export Excel
      </a>
    </div>
  </div>

  <div class="p-4">
    @forelse($submissions as $sub)
    @php
      $pct   = $sub->percent;
      $grade = $sub->grade;
      $gc    = $pct >= 80 ? '#16a34a' : ($pct >= 60 ? '#3b82f6' : ($pct >= 40 ? '#f59e0b' : '#dc2626'));
      $gbg   = $pct >= 80 ? '#dcfce7' : ($pct >= 60 ? '#dbeafe' : ($pct >= 40 ? '#fef9c3' : '#fee2e2'));
    @endphp
    <div class="sub-card">
      <div class="d-flex align-items-start gap-3 flex-wrap">
        {{-- Student info --}}
        <div style="flex:1;min-width:200px;">
          <div class="fw-bold" style="font-size:.95rem;color:#1f2937;">{{ $sub->student->full_name }}</div>
          <div style="font-size:.72rem;color:#6b7280;">
            <i class="bi bi-hash me-1"></i>{{ $sub->student->reg_no ?? '—' }}
            &nbsp;&middot;&nbsp;
            <i class="bi bi-clock me-1"></i>{{ $sub->submitted_at->format('d M Y, h:i A') }}
          </div>
          @if($sub->notes)
          <div class="mt-2 p-2 rounded" style="background:#f8faff;border:1px solid #e5e7eb;font-size:.8rem;color:#374151;">
            {{ $sub->notes }}
          </div>
          @endif
          @if($sub->file_path)
          <div class="mt-2 d-flex align-items-center gap-2">
            @php
              $ext = strtolower(pathinfo($sub->file_path, PATHINFO_EXTENSION));
              $iconMap = ['pdf'=>'bi-file-earmark-pdf','doc'=>'bi-file-earmark-word','docx'=>'bi-file-earmark-word','xlsx'=>'bi-file-earmark-excel','xls'=>'bi-file-earmark-excel','jpg'=>'bi-image','jpeg'=>'bi-image','png'=>'bi-image','zip'=>'bi-file-zip'];
              $icon = $iconMap[$ext] ?? 'bi-file-earmark';
              $filePath = \Illuminate\Support\Facades\Storage::disk('public')->path($sub->file_path);
              $size = file_exists($filePath) ? round(filesize($filePath)/1024, 1).'KB' : '';
            @endphp
            <a href="{{ asset('storage/'.$sub->file_path) }}" target="_blank" download class="btn btn-sm fw-semibold" style="background:#eef2ff;color:#6366f1;font-size:.72rem;border-radius:8px;padding:5px 14px;">
              <i class="bi {{ $icon }} me-1"></i>Download Submission {{ $size ? '('.$size.')' : '' }}
            </a>
            <span style="font-size:.65rem;color:#9ca3af;">{{ strtoupper($ext) }}</span>
          </div>
          @else
          <p class="text-muted" style="font-size:.75rem;margin-top:6px;"><i class="bi bi-chat-text me-1"></i>Text/notes only — no file attached</p>
          @endif
        </div>

        {{-- Current grade display --}}
        @if($sub->status === 'graded')
        <div class="text-center" style="min-width:90px;">
          <div style="font-size:1.6rem;font-weight:800;color:{{ $gc }};">{{ $sub->marks }}<span style="font-size:.9rem;color:#9ca3af;">/{{ $sub->max_marks }}</span></div>
          <span class="grade-badge" style="background:{{ $gbg }};color:{{ $gc }};">{{ $grade }}</span>
          <div style="font-size:.65rem;color:#9ca3af;margin-top:2px;">{{ $pct }}%</div>
        </div>
        @else
        <div class="text-center" style="min-width:90px;">
          <span style="background:#fef9c3;color:#ca8a04;padding:4px 12px;border-radius:20px;font-size:.7rem;font-weight:700;">Pending</span>
        </div>
        @endif
      </div>

      {{-- Grade form --}}
      <form action="{{ route('teacher.assignment.grade', [$assignment->id, $sub->id]) }}" method="POST" class="mt-3 pt-3 border-top">
        @csrf
        <div class="row g-2 align-items-end">
          <div class="col-auto">
            <label style="font-size:.72rem;font-weight:600;color:#374151;display:block;margin-bottom:3px;">Marks</label>
            <input type="number" name="marks" class="grade-pill-input" step="0.5" min="0" max="{{ $sub->max_marks }}" value="{{ $sub->marks ?? '' }}" placeholder="0">
          </div>
          <div class="col-auto" style="padding-top:18px;color:#9ca3af;font-size:.85rem;">/</div>
          <div class="col-auto">
            <label style="font-size:.72rem;font-weight:600;color:#374151;display:block;margin-bottom:3px;">Out of</label>
            <input type="number" name="max_marks" class="grade-pill-input" min="1" max="1000" value="{{ $sub->max_marks ?? 100 }}" placeholder="100">
          </div>
          <div class="col">
            <label style="font-size:.72rem;font-weight:600;color:#374151;display:block;margin-bottom:3px;">Feedback (optional)</label>
            <input type="text" name="feedback" class="feedback-ta" style="height:36px;" value="{{ $sub->feedback }}" placeholder="Great work! / Please redo section 2…">
          </div>
          <div class="col-auto">
            <button type="submit" class="btn btn-sm fw-semibold" style="background:linear-gradient(135deg,#6366f1,#3730a3);color:#fff;border-radius:9px;padding:8px 20px;font-size:.8rem;">
              <i class="bi bi-check-circle me-1"></i>{{ $sub->status === 'graded' ? 'Update' : 'Save Marks' }}
            </button>
          </div>
        </div>
        @if($sub->graded_at)
        <div style="font-size:.65rem;color:#9ca3af;margin-top:5px;">Last graded {{ $sub->graded_at->format('d M Y, h:i A') }}</div>
        @endif
      </form>
    </div>
    @empty
    <div class="text-center text-muted py-5">
      <i class="bi bi-inbox" style="font-size:2.5rem;color:#c7d2fe;"></i>
      <p class="mt-3">No submissions yet for this assignment.</p>
    </div>
    @endforelse
  </div>
</div>
@endsection
