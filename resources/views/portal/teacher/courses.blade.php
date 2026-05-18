@extends('portal.layout')
@section('title','My Courses')
@section('portal-type','Teacher Portal')
@section('sidebar-bg-inline','linear-gradient(180deg,#1e1b4b 0%,#0ea5e9 100%)')
@section('logout-route', route('teacher.logout'))
@section('user-name', session('teacher_name'))
@section('user-role','Teacher')
@section('user-class', session('teacher_class'))
@section('breadcrumb','Courses')

@section('sidebar-nav')
<div class="section-label">Navigation</div>
<a href="{{ route('teacher.dashboard') }}"   class="nav-link"><i class="bi bi-grid-fill"></i>Dashboard</a>
<a href="{{ route('teacher.students') }}" class="nav-link"><i class="bi bi-people-fill"></i>My Students</a>
<a href="{{ route('teacher.courses') }}"     class="nav-link active"><i class="bi bi-book-fill"></i>Courses</a>
<a href="{{ route('teacher.assignments') }}" class="nav-link"><i class="bi bi-file-earmark-pdf-fill"></i>Assignments</a>
<a href="{{ route('teacher.mcqs') }}"        class="nav-link"><i class="bi bi-patch-question-fill"></i>MCQ Tests</a>
<a href="{{ route('teacher.marks') }}"       class="nav-link"><i class="bi bi-bar-chart-fill"></i>Results & Marks</a>
@endsection

@push('styles')
<style>
/* Subject card */
.sub-card{background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,.07);border:1px solid #f1f5f9;cursor:pointer;transition:transform .18s,box-shadow .18s;}
.sub-card:hover{transform:translateY(-4px);box-shadow:0 8px 28px rgba(0,0,0,.13);}
.sub-banner{height:110px;position:relative;display:flex;align-items:flex-end;padding:12px;}
.sub-banner .bg-pattern{position:absolute;inset:0;background:repeating-linear-gradient(45deg,rgba(255,255,255,.06) 0,rgba(255,255,255,.06)10px,transparent 10px,transparent 20px);}
.sub-card-body{padding:16px 18px;}

/* Full-screen drawer modal */
#contentDrawer{position:fixed;inset:0;z-index:9999;display:none;}
#contentDrawer.open{display:flex;}
.drawer-overlay{position:absolute;inset:0;background:rgba(15,23,42,.55);backdrop-filter:blur(3px);}
.drawer-panel{position:relative;margin-left:auto;width:calc(100vw - 240px);max-width:calc(100vw - 240px);height:100vh;background:#f8faff;display:flex;flex-direction:column;box-shadow:-8px 0 40px rgba(0,0,0,.2);animation:slideIn .25s ease;}
@keyframes slideIn{from{transform:translateX(100%)}to{transform:translateX(0)}}
.drawer-head{background:linear-gradient(135deg,#1e1b4b,#3730a3);color:#fff;padding:22px 26px;display:flex;justify-content:space-between;align-items:center;flex-shrink:0;}
.drawer-head h4{margin:0;font-size:1.05rem;font-weight:800;}
.drawer-body{flex:1;overflow-y:auto;display:flex;flex-direction:column;gap:0;}
.drawer-section{padding:20px 24px;border-bottom:1px solid #e5e7eb;}

/* Content type buttons */
.type-btn{border:2px solid #e5e7eb;background:#fff;border-radius:10px;padding:10px 14px;cursor:pointer;text-align:center;transition:all .15s;font-size:.78rem;font-weight:600;}
.type-btn:hover{border-color:#6366f1;background:#eef2ff;}
.type-btn.active{border-color:#6366f1;background:#eef2ff;color:#4f46e5;}
.type-btn i{display:block;font-size:1.4rem;margin-bottom:4px;}

/* Content item card */
.content-item{background:#fff;border-radius:12px;border:1px solid #e5e7eb;padding:14px 16px;margin-bottom:10px;position:relative;}
.content-item .ci-type{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:.65rem;font-weight:700;text-transform:uppercase;margin-bottom:6px;}
.content-item .ci-title{font-weight:700;font-size:.92rem;color:#1f2937;}
.content-item .ci-desc{font-size:.78rem;color:#6b7280;margin-top:3px;}
.content-item .ci-del{position:absolute;top:12px;right:12px;background:#fee2e2;color:#dc2626;border:none;border-radius:8px;padding:5px 10px;font-size:.72rem;cursor:pointer;font-weight:600;}
.content-item .ci-del:hover{background:#fecaca;}
.yt-thumb{border-radius:8px;overflow:hidden;margin-top:8px;max-width:640px;}
.yt-thumb iframe{width:100%;aspect-ratio:16/9;border:none;border-radius:8px;background:#000;}

/* Form styles inside drawer */
.drawer-form input,.drawer-form textarea,.drawer-form select{border:1.5px solid #e5e7eb;border-radius:9px;padding:9px 12px;width:100%;font-size:.85rem;transition:border .15s;font-family:'Inter',sans-serif;}
.drawer-form input:focus,.drawer-form textarea:focus,.drawer-form select:focus{border-color:#6366f1;outline:none;}
.drawer-form label{font-size:.78rem;font-weight:600;color:#374151;margin-bottom:4px;display:block;}
.yt-preview{border-radius:10px;overflow:hidden;margin-top:10px;display:none;}
.yt-preview iframe{width:100%;aspect-ratio:16/9;border:none;}
</style>
@endpush

@section('content')
<div class="hello-banner" style="background:linear-gradient(135deg,#eff6ff 0%,#f0fdf4 100%);border:1px solid #dbeafe;">
  <div>
    <h2 style="color:#1e3a5f;font-size:1.5rem;">Course Content Manager</h2>
    <p style="color:#374151;margin:0;">Class {{ $teacher->class }} Teacher &nbsp;<span style="color:#9ca3af;">&middot;</span>&nbsp; Click any subject to manage its content</p>
  </div>
</div>

@if($subjects->isEmpty())
<div class="section-card p-5 text-center text-muted">
  <i class="bi bi-book" style="font-size:2.5rem;color:#c7d2fe;"></i>
  <p class="mt-3">No subjects assigned to you yet.</p>
</div>
@else

@php
$palettes=[
  ['bg'=>'linear-gradient(135deg,#8b5cf6,#a78bfa)','icon'=>'bi-book'],
  ['bg'=>'linear-gradient(135deg,#3b82f6,#60a5fa)','icon'=>'bi-laptop'],
  ['bg'=>'linear-gradient(135deg,#06b6d4,#67e8f9)','icon'=>'bi-code-slash'],
  ['bg'=>'linear-gradient(135deg,#10b981,#6ee7b7)','icon'=>'bi-bar-chart-line'],
  ['bg'=>'linear-gradient(135deg,#f59e0b,#fcd34d)','icon'=>'bi-lightning-fill'],
  ['bg'=>'linear-gradient(135deg,#ec4899,#f9a8d4)','icon'=>'bi-mortarboard'],
];
@endphp

<div class="row g-3">
  @foreach($subjects as $i => $sub)
  @php $pal = $palettes[$i % count($palettes)]; @endphp
  <div class="col-md-6 col-lg-4">
    <div class="sub-card" onclick="openDrawer({{ $sub->id }}, '{{ addslashes($sub->subject_name) }}', '{{ $sub->subject_code }}', '{{ route('teacher.course.content.store', $sub->id) }}', '{{ route('teacher.course.content', $sub->id) }}')">
      <div class="sub-banner" style="background:{{ $pal['bg'] }};">
        <div class="bg-pattern"></div>
        <i class="bi {{ $pal['icon'] }}" style="position:absolute;right:16px;top:50%;transform:translateY(-50%);font-size:3rem;color:rgba(255,255,255,.25);"></i>
        <span style="position:relative;z-index:1;background:rgba(255,255,255,.2);color:#fff;padding:3px 12px;border-radius:20px;font-size:.68rem;font-weight:700;">{{ $sub->subject_code }}</span>
      </div>
      <div class="sub-card-body">
        <div style="font-weight:800;font-size:1rem;color:#1f2937;">{{ $sub->subject_name }}</div>
        <div style="font-size:.75rem;color:#9ca3af;margin-top:4px;">{{ Str::limit($sub->description,60) }}</div>
        <div class="mt-3 d-flex align-items-center gap-2">
          <span style="background:#eef2ff;color:#6366f1;font-size:.7rem;padding:4px 12px;border-radius:20px;font-weight:700;">
            <i class="bi bi-files me-1"></i>Manage Content
          </span>
        </div>
      </div>
    </div>
  </div>
  @endforeach
</div>
@endif

{{-- ══ CONTENT DRAWER ══════════════════════════════════════════ --}}
<div id="contentDrawer">
  <div class="drawer-overlay" onclick="closeDrawer()"></div>
  <div class="drawer-panel">
    {{-- Header --}}
    <div class="drawer-head">
      <div>
        <h4 id="drawerTitle">Subject Content</h4>
        <small id="drawerCode" style="opacity:.7;font-size:.72rem;"></small>
      </div>
      <button onclick="closeDrawer()" style="background:rgba(255,255,255,.15);border:none;color:#fff;border-radius:8px;padding:7px 14px;cursor:pointer;font-size:.85rem;font-weight:600;"><i class="bi bi-x-lg"></i></button>
    </div>

    <div class="drawer-body">
      {{-- Add New Content --}}
      <div class="drawer-section">
        <div class="fw-bold mb-3" style="font-size:.85rem;color:#374151;"><i class="bi bi-plus-circle me-2 text-primary"></i>Add New Content</div>

        {{-- Type selector --}}
        <div class="row g-2 mb-3" id="typeGrid">
          <div class="col-4 col-sm-2"><div class="type-btn" onclick="selectType('text')" id="btn-text"><i class="bi bi-text-paragraph"></i>Text</div></div>
          <div class="col-4 col-sm-2"><div class="type-btn" onclick="selectType('pdf')" id="btn-pdf"><i class="bi bi-file-pdf"></i>PDF</div></div>
          <div class="col-4 col-sm-2"><div class="type-btn" onclick="selectType('video')" id="btn-video"><i class="bi bi-camera-video"></i>Video</div></div>
          <div class="col-4 col-sm-2"><div class="type-btn" onclick="selectType('link')" id="btn-link"><i class="bi bi-link-45deg"></i>Link</div></div>
          <div class="col-4 col-sm-2"><div class="type-btn" onclick="selectType('youtube')" id="btn-youtube"><i class="bi bi-youtube"></i>YouTube</div></div>
        </div>

        <form id="contentForm" class="drawer-form" enctype="multipart/form-data">
          @csrf
          <input type="hidden" name="type" id="fType" value="text">

          <div class="mb-3">
            <label>Title <span class="text-danger">*</span></label>
            <input type="text" name="title" required placeholder="Content title…">
          </div>
          <div class="mb-3">
            <label>Description</label>
            <textarea name="description" rows="2" placeholder="Optional description…"></textarea>
          </div>

          {{-- Text content --}}
          <div id="field-text" class="mb-3">
            <label>Content Text</label>
            <textarea name="content_text" rows="5" placeholder="Write your content here…"></textarea>
          </div>

          {{-- File (pdf / video) --}}
          <div id="field-file" class="mb-3" style="display:none;">
            <label id="fileLabel">Upload File</label>
            <input type="file" name="file" id="fileInput">
          </div>

          {{-- URL (link) --}}
          <div id="field-url" class="mb-3" style="display:none;">
            <label>URL</label>
            <input type="url" name="url" id="urlInput" placeholder="https://…">
          </div>

          {{-- YouTube --}}
          <div id="field-youtube" class="mb-3" style="display:none;">
            <label>YouTube URL</label>
            <input type="url" name="url" id="ytInput" placeholder="https://www.youtube.com/watch?v=…" oninput="updateYtPreview(this.value)">
            <div class="yt-preview" id="ytPreview">
              <iframe id="ytIframe" src="" allowfullscreen></iframe>
            </div>
          </div>

          <div id="addError" class="text-danger" style="font-size:.78rem;display:none;"></div>

          <button type="submit" class="btn fw-semibold w-100 mt-2" id="addBtn" style="background:linear-gradient(135deg,#6366f1,#3730a3);color:#fff;border-radius:10px;padding:11px;">
            <i class="bi bi-plus-circle me-2"></i>Add Content
          </button>
        </form>
      </div>

      {{-- Existing content list --}}
      <div class="drawer-section" style="flex:1;">
        <div class="fw-bold mb-3" style="font-size:.85rem;color:#374151;"><i class="bi bi-collection me-2 text-primary"></i>Published Content</div>
        <div id="contentList"><div class="text-muted text-center py-4"><i class="bi bi-hourglass-split me-2"></i>Loading…</div></div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
let currentSubjectId = null;
let currentContentUrl = null;
let currentStoreUrl   = null;

const typeIcons = { text:'bi-text-paragraph', pdf:'bi-file-pdf-fill', video:'bi-camera-video-fill', link:'bi-link-45deg', youtube:'bi-youtube' };
const typeColors= { text:'#6366f1', pdf:'#dc2626', video:'#7c3aed', link:'#0ea5e9', youtube:'#ff0000' };
const typeBg    = { text:'#eef2ff', pdf:'#fee2e2', video:'#ede9fe', link:'#e0f2fe', youtube:'#fff1f2' };

function openDrawer(id, name, code, storeUrl, contentUrl) {
  currentSubjectId  = id;
  currentStoreUrl   = storeUrl;
  currentContentUrl = contentUrl;
  document.getElementById('drawerTitle').textContent = name;
  document.getElementById('drawerCode').textContent  = code;
  document.getElementById('contentDrawer').classList.add('open');
  document.body.style.overflow = 'hidden';
  selectType('text');
  loadContent();
}
function closeDrawer() {
  document.getElementById('contentDrawer').classList.remove('open');
  document.body.style.overflow = '';
}

function selectType(type) {
  document.getElementById('fType').value = type;
  // Reset button styles
  ['text','pdf','video','link','youtube'].forEach(t => {
    document.getElementById('btn-'+t).classList.toggle('active', t === type);
  });
  // Show/hide fields
  document.getElementById('field-text').style.display    = type==='text'    ? '' : 'none';
  document.getElementById('field-file').style.display    = (type==='pdf'||type==='video') ? '' : 'none';
  document.getElementById('field-url').style.display     = type==='link'    ? '' : 'none';
  document.getElementById('field-youtube').style.display = type==='youtube' ? '' : 'none';

  if (type==='pdf')   document.getElementById('fileLabel').textContent = 'Upload PDF';
  if (type==='video') document.getElementById('fileLabel').textContent = 'Upload Video (mp4)';
  // Update file input accept
  if (type==='pdf')   document.getElementById('fileInput').accept = '.pdf';
  if (type==='video') document.getElementById('fileInput').accept = 'video/*';
}

function updateYtPreview(url) {
  const match = url.match(/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\n\?#]{11})/);
  const preview = document.getElementById('ytPreview');
  if (match) {
    document.getElementById('ytIframe').src = 'https://www.youtube.com/embed/' + match[1] + '?rel=0';
    preview.style.display = '';
  } else {
    preview.style.display = 'none';
    document.getElementById('ytIframe').src = '';
  }
}

function loadContent() {
  document.getElementById('contentList').innerHTML = '<div class="text-muted text-center py-4"><i class="bi bi-hourglass-split me-2"></i>Loading…</div>';
  fetch(currentContentUrl, { headers:{'X-Requested-With':'XMLHttpRequest'} })
    .then(r => r.json())
    .then(data => renderContentList(data.contents))
    .catch(() => { document.getElementById('contentList').innerHTML = '<div class="text-danger">Failed to load content.</div>'; });
}

function renderContentList(contents) {
  const el = document.getElementById('contentList');
  if (!contents.length) {
    el.innerHTML = '<div class="text-center text-muted py-4"><i class="bi bi-inbox" style="font-size:2rem;"></i><p class="mt-2">No content yet. Add some above!</p></div>';
    return;
  }
  el.innerHTML = contents.map(c => {
    const icon  = typeIcons[c.type] || 'bi-file';
    const color = typeColors[c.type] || '#6366f1';
    const bg    = typeBg[c.type]    || '#eef2ff';
    let body = '';
    if (c.type==='text' && c.content_text) body = `<div class="ci-desc mt-2" style="white-space:pre-wrap;max-height:100px;overflow:auto;border:1px solid #f1f5f9;padding:8px;border-radius:8px;font-size:.78rem;">${esc(c.content_text)}</div>`;
    if (c.type==='pdf' && c.file_url)  body = `<a href="${c.file_url}" target="_blank" class="btn btn-sm mt-2" style="background:#fee2e2;color:#dc2626;font-size:.72rem;border-radius:8px;"><i class="bi bi-download me-1"></i>Open PDF</a>`;
    if (c.type==='video' && c.file_url) body = `<video controls style="width:100%;border-radius:8px;margin-top:8px;max-height:180px;"><source src="${c.file_url}">Your browser does not support video.</video>`;
    if (c.type==='link' && c.url)      body = `<a href="${c.url}" target="_blank" class="d-block mt-1" style="font-size:.78rem;color:#0ea5e9;word-break:break-all;">${esc(c.url)}</a>`;
    if (c.type==='youtube' && c.youtube_embed) body = `<div class="yt-thumb"><iframe src="${c.youtube_embed}" allowfullscreen></iframe></div>`;

    const delBtn = c.mine ? `<button class="ci-del" onclick="deleteContent(${c.id}, event)"><i class="bi bi-trash me-1"></i>Remove</button>` : '';
    return `<div class="content-item" id="ci-${c.id}">
      ${delBtn}
      <div class="d-flex flex-wrap align-items-center gap-2 mb-2" style="${c.mine ? 'padding-right:85px;' : ''}">
        <div class="ci-type m-0" style="background:${bg};color:${color};"><i class="bi ${icon}"></i>${c.type.toUpperCase()}</div>
        <div style="font-size:.72rem;color:#0f766e;background:#ccfbf1;padding:4px 14px;border-radius:20px;font-weight:700;border:1px solid #5eead4;"><i class="bi bi-person-fill me-1"></i>${esc(c.teacher)} &nbsp;·&nbsp; ${c.created_at}</div>
      </div>
      <div class="ci-title">${esc(c.title)}</div>
      ${c.description ? `<div class="ci-desc">${esc(c.description)}</div>` : ''}
      ${body}
    </div>`;
  }).join('');
}

function esc(str) { const d=document.createElement('div');d.textContent=str||'';return d.innerHTML; }

async function deleteContent(id, e) {
  e.stopPropagation();
  const ok = await window.portalConfirm({
    title: 'Remove Content',
    message: 'Remove this content item from the course?',
    confirmText: 'Remove',
    danger: true
  });
  if (!ok) return;
  const base = currentContentUrl.replace('/content', '');
  fetch(base + '/content/' + id, {
    method:'DELETE',
    headers:{'X-CSRF-TOKEN':'{{ csrf_token() }}','X-Requested-With':'XMLHttpRequest'}
  }).then(r=>r.json()).then(data => {
    if (data.success) document.getElementById('ci-'+id).remove();
  });
}

// Form submit
document.getElementById('contentForm').addEventListener('submit', function(e) {
  e.preventDefault();
  const btn = document.getElementById('addBtn');
  const err = document.getElementById('addError');
  err.style.display = 'none';
  btn.disabled = true; btn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Adding…';

  const fd = new FormData(this);
  fetch(currentStoreUrl, { method:'POST', body:fd, headers:{'X-Requested-With':'XMLHttpRequest'} })
    .then(r=>r.json())
    .then(data => {
      if (data.success) {
        this.reset();
        selectType('text');
        document.getElementById('ytPreview').style.display='none';
        loadContent();
      } else {
        err.textContent = data.message || 'Error adding content.';
        err.style.display = '';
      }
    })
    .catch(() => { err.textContent='Network error. Please try again.'; err.style.display=''; })
    .finally(() => { btn.disabled=false; btn.innerHTML='<i class="bi bi-plus-circle me-2"></i>Add Content'; });
});
</script>
@endpush
@endsection
