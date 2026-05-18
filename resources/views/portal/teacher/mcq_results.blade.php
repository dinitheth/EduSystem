@extends('portal.layout')
@section('title','MCQ Results')
@section('portal-type','Teacher Portal')
@section('sidebar-bg-inline','linear-gradient(180deg,#1e1b4b 0%,#0ea5e9 100%)')
@section('logout-route', route('teacher.logout'))
@section('user-name', session('teacher_name'))
@section('user-role','Teacher')
@section('user-class', session('teacher_class'))
@section('breadcrumb','MCQ Tests → Results')

@section('sidebar-nav')
<div class="section-label">Navigation</div>
<a href="{{ route('teacher.dashboard') }}" class="nav-link"><i class="bi bi-grid-fill"></i>Dashboard</a>
<a href="{{ route('teacher.students') }}" class="nav-link"><i class="bi bi-people-fill"></i>My Students</a>
<a href="{{ route('teacher.assignments') }}" class="nav-link"><i class="bi bi-file-earmark-pdf-fill"></i>Assignments</a>
<a href="{{ route('teacher.mcqs') }}" class="nav-link active"><i class="bi bi-patch-question-fill"></i>MCQ Tests</a>
<a href="{{ route('teacher.marks') }}" class="nav-link"><i class="bi bi-bar-chart-fill"></i>Results & Marks</a>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h5 class="fw-bold mb-0">Results: {{ $mcq->title }}</h5>
    <span style="font-size:.78rem;color:#9ca3af;">Class {{ $mcq->class }} &nbsp;·&nbsp; {{ $mcq->subject->subject_name ?? 'General' }} &nbsp;·&nbsp; {{ $submissions->count() }} submissions</span>
  </div>
  <a href="{{ route('teacher.mcqs') }}" class="btn btn-sm btn-outline-secondary">← Back to MCQs</a>
</div>

<div class="section-card">
  <div class="table-responsive">
    <table class="table table-hover mb-0" style="font-size:.875rem;">
      <thead style="background:#f8faff;">
        <tr><th>#</th><th>Student</th><th>Score</th><th>Percentage</th><th>Grade</th><th>Submitted</th></tr>
      </thead>
      <tbody>
        @forelse($submissions as $i => $s)
        @php $pct = $s->total > 0 ? round(($s->score/$s->total)*100) : 0; @endphp
        <tr>
          <td class="text-muted">{{ $i+1 }}</td>
          <td class="fw-semibold">{{ $s->student->full_name ?? '—' }}</td>
          <td><strong>{{ $s->score }}/{{ $s->total }}</strong></td>
          <td>
            <div class="d-flex align-items-center gap-2">
              <div style="flex:1;background:#f1f5f9;border-radius:4px;height:6px;">
                <div style="background:{{ $pct>=75?'#16a34a':($pct>=50?'#f59e0b':'#dc2626') }};width:{{ $pct }}%;height:6px;border-radius:4px;"></div>
              </div>
              <span style="font-weight:700;color:{{ $pct>=75?'#16a34a':($pct>=50?'#f59e0b':'#dc2626') }};width:38px;">{{ $pct }}%</span>
            </div>
          </td>
          <td>
            @if($pct>=75)<span class="badge bg-success">A</span>
            @elseif($pct>=60)<span class="badge bg-primary">B</span>
            @elseif($pct>=40)<span class="badge bg-warning text-dark">C</span>
            @else<span class="badge bg-danger">F</span>
            @endif
          </td>
          <td class="text-muted">{{ $s->submitted_at ? \Carbon\Carbon::parse($s->submitted_at)->format('d M Y, H:i') : '—' }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center text-muted py-4">No submissions yet.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
