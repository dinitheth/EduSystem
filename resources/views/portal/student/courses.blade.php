@extends('portal.layout')
@section('title','My Courses')
@section('portal-type','Student Portal')
@section('sidebar-bg-inline','linear-gradient(180deg,#0f4c75 0%,#0d7377 100%)')
@section('logout-route', route('student.logout'))
@section('user-name', session('student_name'))
@section('user-role','Student')
@section('user-class', session('student_class'))
@section('breadcrumb','Courses')

@section('sidebar-nav')
<div class="section-label">Navigation</div>
<a href="{{ route('student.dashboard') }}" class="nav-link"><i class="bi bi-grid-fill"></i>Dashboard</a>
<a href="{{ route('student.courses') }}" class="nav-link active"><i class="bi bi-book-fill"></i>Courses</a>
<a href="{{ route('student.assignments') }}" class="nav-link"><i class="bi bi-file-earmark-pdf-fill"></i>Assignments</a>
<a href="{{ route('student.mcqs') }}" class="nav-link"><i class="bi bi-patch-question-fill"></i>MCQ Tests</a>
<a href="{{ route('student.marks') }}" class="nav-link"><i class="bi bi-bar-chart-fill"></i>My Marks</a>
@endsection

@push('styles')
<style>
/* Scrollable recent row */
.recent-scroll{display:flex;gap:16px;overflow-x:auto;padding-bottom:8px;scrollbar-width:thin;}
.recent-scroll::-webkit-scrollbar{height:4px;}
.recent-scroll::-webkit-scrollbar-thumb{background:#cbd5e1;border-radius:4px;}
.recent-card{flex:0 0 210px;background:#fff;border-radius:12px;box-shadow:0 2px 8px rgba(0,0,0,.07);border:1px solid #f1f5f9;overflow:hidden;cursor:pointer;transition:transform .18s,box-shadow .18s;text-decoration:none;}
.recent-card:hover{transform:translateY(-3px);box-shadow:0 6px 18px rgba(0,0,0,.12);}
.rc-banner{height:100px;position:relative;overflow:hidden;}
.rc-banner .rp{position:absolute;inset:0;background:repeating-linear-gradient(45deg,rgba(255,255,255,.07) 0,rgba(255,255,255,.07)10px,transparent 10px,transparent 20px);}
.rc-body{padding:11px 14px;}
.rc-cat{font-size:.65rem;color:#9ca3af;font-weight:700;text-transform:uppercase;letter-spacing:.5px;}
.rc-name{font-size:.875rem;font-weight:700;color:#1f2937;margin-top:2px;line-height:1.3;}

/* Overview cards */
.ov-card{background:#fff;border-radius:14px;box-shadow:0 2px 8px rgba(0,0,0,.07);border:1px solid #f1f5f9;overflow:hidden;cursor:pointer;transition:transform .18s,box-shadow .18s;}
.ov-card:hover{transform:translateY(-3px);box-shadow:0 8px 24px rgba(0,0,0,.1);}
.ov-banner{height:88px;position:relative;display:flex;align-items:flex-end;padding:10px;}
.ov-banner .rp{position:absolute;inset:0;background:repeating-linear-gradient(45deg,rgba(255,255,255,.07) 0,rgba(255,255,255,.07)10px,transparent 10px,transparent 20px);}
.ov-body{padding:15px 17px;}
.ov-name{font-size:.95rem;font-weight:700;color:#1f2937;}
.ov-teacher{font-size:.75rem;color:#6b7280;margin-top:3px;}
.ov-stats{display:flex;gap:10px;margin-top:10px;padding-top:8px;border-top:1px solid #f1f5f9;}
.ov-stat{text-align:center;flex:1;}
.ov-stat .n{font-size:1.05rem;font-weight:800;color:#1f2937;}
.ov-stat .l{font-size:.63rem;color:#9ca3af;font-weight:600;}
.prog-bar{height:5px;background:#f1f5f9;border-radius:4px;margin-top:8px;overflow:hidden;}
.prog-fill{height:5px;border-radius:4px;}
.view-btn{display:flex;align-items:center;justify-content:center;gap:6px;margin-top:12px;padding:8px;border-radius:9px;font-size:.78rem;font-weight:700;color:#6366f1;background:#eef2ff;border:none;width:100%;cursor:pointer;transition:background .15s;}
.view-btn:hover{background:#e0e7ff;}

/* Content drawer (read-only) */
#viewDrawer{position:fixed;inset:0;z-index:9999;display:none;}
#viewDrawer.open{display:flex;}
.vd-overlay{position:absolute;inset:0;background:rgba(15,23,42,.55);backdrop-filter:blur(3px);}
.vd-panel{position:relative;margin-left:auto;width:calc(100vw - 240px);max-width:calc(100vw - 240px);height:100vh;background:#f8faff;display:flex;flex-direction:column;box-shadow:-8px 0 40px rgba(0,0,0,.2);animation:slideIn .25s ease;}
@keyframes slideIn{from{transform:translateX(100%)}to{transform:translateX(0)}}
.vd-head{background:linear-gradient(135deg,#0f4c75,#0d7377);color:#fff;padding:20px 24px;display:flex;justify-content:space-between;align-items:center;flex-shrink:0;}
.vd-head h4{margin:0;font-size:1rem;font-weight:800;}
.vd-body{flex:1;overflow-y:auto;padding:22px;}

/* Content item in drawer */
.ci{background:#fff;border-radius:12px;border:1px solid #e5e7eb;padding:16px 18px;margin-bottom:12px;}
.ci-badge{display:inline-flex;align-items:center;gap:5px;padding:3px 11px;border-radius:20px;font-size:.65rem;font-weight:700;text-transform:uppercase;margin-bottom:8px;}
.ci-title{font-weight:700;font-size:.92rem;color:#1f2937;margin-bottom:4px;}
.ci-desc{font-size:.78rem;color:#6b7280;margin-bottom:8px;}
.ci-text{background:#f8faff;border:1px solid #e5e7eb;border-radius:8px;padding:12px;font-size:.82rem;color:#374151;white-space:pre-wrap;line-height:1.6;max-height:240px;overflow-y:auto;}
.yt-embed{border-radius:10px;overflow:hidden;margin-top:6px;}
.yt-embed iframe{width:100%;aspect-ratio:16/9;border:none;}
.ci-footer{font-size:.65rem;color:#9ca3af;margin-top:8px;}
</style>
@endpush

@section('content')
@php
$palettes=[
  ['bg'=>'linear-gradient(135deg,#8b5cf6,#a78bfa)','icon'=>'bi-book'],
  ['bg'=>'linear-gradient(135deg,#3b82f6,#60a5fa)','icon'=>'bi-laptop'],
  ['bg'=>'linear-gradient(135deg,#06b6d4,#67e8f9)','icon'=>'bi-code-slash'],
  ['bg'=>'linear-gradient(135deg,#10b981,#6ee7b7)','icon'=>'bi-bar-chart-line'],
  ['bg'=>'linear-gradient(135deg,#f59e0b,#fcd34d)','icon'=>'bi-lightning-fill'],
  ['bg'=>'linear-gradient(135deg,#ec4899,#f9a8d4)','icon'=>'bi-mortarboard'],
  ['bg'=>'linear-gradient(135deg,#6366f1,#818cf8)','icon'=>'bi-globe'],
  ['bg'=>'linear-gradient(135deg,#14b8a6,#5eead4)','icon'=>'bi-graph-up'],
];
@endphp

{{-- Recently accessed --}}
<div class="mb-4">
  <h5 class="fw-bold mb-3" style="color:#374151;font-size:.95rem;">
    <i class="bi bi-clock-history me-2 text-primary"></i>Recently accessed courses
  </h5>
  @if($subjects->isEmpty())
    <div class="section-card p-4 text-center text-muted">No courses enrolled yet.</div>
  @else
  <div class="recent-scroll">
    @foreach($subjects as $i => $sub)
    @php $pal = $palettes[$i % count($palettes)]; @endphp
    <div class="recent-card" onclick="openDrawer({{ $sub->id }}, '{{ addslashes($sub->subject_name) }}', '{{ $sub->subject_code }}', '{{ route('student.course.content', $sub->id) }}', '{{ $pal['bg'] }}')">
      <div class="rc-banner" style="background:{{ $pal['bg'] }};">
        <div class="rp"></div>
        <i class="bi {{ $pal['icon'] }}" style="position:absolute;right:12px;top:50%;transform:translateY(-50%);font-size:2.5rem;color:rgba(255,255,255,.25);"></i>
      </div>
      <div class="rc-body">
        <div class="rc-cat">{{ $sub->category ?? 'Class ' . session('student_class') }}</div>
        <div class="rc-name">{{ $sub->subject_name }}</div>
        <div style="font-size:.68rem;color:#9ca3af;margin-top:2px;">{{ $sub->subject_code }}</div>
      </div>
    </div>
    @endforeach
  </div>
  @endif
</div>

{{-- Course overview --}}
<h5 class="fw-bold mb-3" style="color:#374151;font-size:.95rem;">
  <i class="bi bi-grid-3x3-gap me-2 text-primary"></i>Course overview
</h5>

@if($subjects->isEmpty())
  <div class="section-card p-4 text-center text-muted"><i class="bi bi-inbox" style="font-size:2rem;"></i><p class="mt-2">No courses assigned.</p></div>
@else
<div class="row g-3">
  @foreach($subjects as $i => $sub)
  @php
    $pal    = $palettes[$i % count($palettes)];
    $avg    = round($sub->mark_avg ?? 0);
    $grade  = $avg >= 80 ? 'A' : ($avg >= 60 ? 'B' : ($avg >= 40 ? 'C' : ($avg > 0 ? 'F' : '—')));
    $pColor = $avg >= 60 ? '#16a34a' : ($avg >= 40 ? '#f59e0b' : '#6366f1');
    $teacher= $sub->teachers->first();
  @endphp
  <div class="col-md-6 col-lg-4">
    <div class="ov-card h-100" onclick="openDrawer({{ $sub->id }}, '{{ addslashes($sub->subject_name) }}', '{{ $sub->subject_code }}', '{{ route('student.course.content', $sub->id) }}', '{{ $pal['bg'] }}')" title="Click to view course content">
      <div class="ov-banner" style="background:{{ $pal['bg'] }};">
        <div class="rp"></div>
        <i class="bi {{ $pal['icon'] }}" style="position:absolute;right:14px;top:50%;transform:translateY(-50%);font-size:2.8rem;color:rgba(255,255,255,.2);"></i>
        <span style="position:relative;z-index:1;background:rgba(255,255,255,.2);color:#fff;padding:2px 10px;border-radius:20px;font-size:.68rem;font-weight:700;">{{ $sub->subject_code }}</span>
      </div>
      <div class="ov-body">
        <div class="ov-name">{{ $sub->subject_name }}</div>
        <div class="ov-teacher"><i class="bi bi-person-fill me-1"></i>{{ $teacher ? $teacher->full_name : 'Not assigned' }}</div>
        @if($sub->description)
        <div style="font-size:.73rem;color:#9ca3af;margin-top:4px;">{{ Str::limit($sub->description,70) }}</div>
        @endif
        <div class="ov-stats">
          <div class="ov-stat"><div class="n" style="color:#f59e0b;">{{ $sub->assignment_count }}</div><div class="l">Assignments</div></div>
          <div class="ov-stat"><div class="n" style="color:#6366f1;">{{ $sub->mcq_count }}</div><div class="l">MCQ Tests</div></div>
          <div class="ov-stat"><div class="n" style="color:{{ $pColor }};">{{ $avg > 0 ? $avg.'%' : '—' }}</div><div class="l">My Avg</div></div>
          <div class="ov-stat"><div class="n" style="color:{{ $pColor }};">{{ $grade }}</div><div class="l">Grade</div></div>
        </div>
        @if($avg > 0)
        <div class="prog-bar mt-2"><div class="prog-fill" style="width:{{ $avg }}%;background:{{ $pColor }};"></div></div>
        <div style="font-size:.65rem;color:#9ca3af;margin-top:2px;">Performance: {{ $avg }}%</div>
        @endif
        <div class="d-flex gap-2 mt-3">
          <a href="{{ route('student.assignments') }}" class="btn btn-sm fw-semibold flex-fill" style="background:#fef9c3;color:#ca8a04;border-radius:8px;font-size:.72rem;padding:5px;" onclick="event.stopPropagation()">
            <i class="bi bi-file-earmark me-1"></i>Assignments
          </a>
          <a href="{{ route('student.mcqs') }}" class="btn btn-sm fw-semibold flex-fill" style="background:#eef2ff;color:#6366f1;border-radius:8px;font-size:.72rem;padding:5px;" onclick="event.stopPropagation()">
            <i class="bi bi-patch-question me-1"></i>MCQs
          </a>
        </div>
      </div>
    </div>
  </div>
  @endforeach
</div>
@endif

{{-- Content View Drawer --}}
<div id="viewDrawer">
  <div class="vd-overlay" onclick="closeDrawer()"></div>
  <div class="vd-panel">
    <div class="vd-head" id="drawerHead">
      <div>
        <h4 id="drawerTitle">Course Content</h4>
        <small id="drawerCode" style="opacity:.7;font-size:.72rem;"></small>
      </div>
      <button onclick="closeDrawer()" style="background:rgba(255,255,255,.15);border:none;color:#fff;border-radius:8px;padding:7px 14px;cursor:pointer;font-weight:600;"><i class="bi bi-x-lg"></i></button>
    </div>
    <div class="vd-body" id="drawerBody">
      <div class="text-center text-muted py-5"><i class="bi bi-hourglass-split" style="font-size:2rem;"></i><p>Loading…</p></div>
    </div>
  </div>
</div>

@push('scripts')
<script>
const typeIcons  = { text:'bi-text-paragraph', pdf:'bi-file-pdf-fill', video:'bi-camera-video-fill', link:'bi-link-45deg', youtube:'bi-youtube' };
const typeColors = { text:'#6366f1', pdf:'#dc2626', video:'#7c3aed', link:'#0ea5e9', youtube:'#ff0000' };
const typeBg     = { text:'#eef2ff', pdf:'#fee2e2', video:'#ede9fe', link:'#e0f2fe', youtube:'#fff1f2' };

function openDrawer(id, name, code, url, bg) {
  document.getElementById('drawerTitle').textContent = name;
  document.getElementById('drawerCode').textContent  = code;
  document.getElementById('drawerHead').style.background = bg || 'linear-gradient(135deg,#0f4c75,#0d7377)';
  document.getElementById('viewDrawer').classList.add('open');
  document.body.style.overflow = 'hidden';
  fetch(url, { headers:{'X-Requested-With':'XMLHttpRequest'} })
    .then(r => r.json())
    .then(data => renderContent(data.contents))
    .catch(() => { document.getElementById('drawerBody').innerHTML = '<div class="text-danger text-center py-4">Failed to load content.</div>'; });
}
function closeDrawer() {
  document.getElementById('viewDrawer').classList.remove('open');
  document.body.style.overflow = '';
}

function renderContent(contents) {
  const body = document.getElementById('drawerBody');
  if (!contents.length) {
    body.innerHTML = `<div class="text-center text-muted py-5">
      <i class="bi bi-inbox" style="font-size:2.5rem;color:#c7d2fe;"></i>
      <p class="mt-3">No content published for this course yet.</p>
      <p style="font-size:.8rem;">Check back later or contact your teacher.</p>
    </div>`;
    return;
  }
  body.innerHTML = contents.map(c => {
    const icon  = typeIcons[c.type]  || 'bi-file';
    const color = typeColors[c.type] || '#6366f1';
    const bg    = typeBg[c.type]    || '#eef2ff';
    let media = '';
    if (c.type==='text' && c.content_text)
      media = `<div class="ci-text">${esc(c.content_text)}</div>`;
    if (c.type==='pdf' && c.file_url)
      media = `<a href="${c.file_url}" target="_blank" class="btn btn-sm" style="background:#fee2e2;color:#dc2626;font-size:.75rem;border-radius:8px;padding:7px 16px;font-weight:600;"><i class="bi bi-download me-1"></i>Open / Download PDF</a>`;
    if (c.type==='video' && c.file_url)
      media = `<video controls style="width:100%;border-radius:10px;max-height:220px;"><source src="${c.file_url}">Your browser does not support video.</video>`;
    if (c.type==='link' && c.url)
      media = `<a href="${c.url}" target="_blank" style="color:#0ea5e9;font-size:.82rem;word-break:break-all;"><i class="bi bi-box-arrow-up-right me-1"></i>${esc(c.url)}</a>`;
    if (c.type==='youtube' && c.youtube_embed)
      media = `<div class="yt-embed"><iframe src="${c.youtube_embed}" allowfullscreen allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe></div>`;
    return `<div class="ci">
      <div class="ci-badge" style="background:${bg};color:${color};"><i class="bi ${icon}"></i>${c.type.toUpperCase()}</div>
      <div class="ci-title">${esc(c.title)}</div>
      ${c.description ? `<div class="ci-desc">${esc(c.description)}</div>` : ''}
      ${media}
      <div class="ci-footer"><i class="bi bi-person me-1"></i>${esc(c.teacher)} &nbsp;·&nbsp; ${c.created_at}</div>
    </div>`;
  }).join('');
}
function esc(s){const d=document.createElement('div');d.textContent=s||'';return d.innerHTML;}
</script>
@endpush
@endsection
