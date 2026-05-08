@extends('layouts.app')
@section('title','Subjects')
@section('page-title','Subjects')
@section('page-subtitle','Manage school subjects')

@section('content')

<div class="card">
    <div class="card-header text-white py-3 d-flex flex-wrap gap-2 justify-content-between align-items-center"
         style="background:linear-gradient(135deg,#10b981,#34d399);">
        <span><i class="bi bi-book-fill me-2"></i>All Subjects
            <span class="badge bg-white text-success rounded-pill ms-2" id="subject-count">{{ $subjects->count() }} subjects</span>
        </span>
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <div class="input-group" style="width:260px;">
                <span class="input-group-text bg-white border-end-0 py-1"><i class="bi bi-search text-muted small"></i></span>
                <input type="text" id="searchInput" class="form-control border-start-0 ps-0 py-1"
                    placeholder="Search Code or Subject Name..." style="font-size:.85rem;" autocomplete="off">
                <button class="btn btn-outline-secondary btn-sm" id="clearSearch" style="display:none;"><i class="bi bi-x-lg"></i></button>
            </div>
            <button type="button" class="btn btn-sm btn-outline-light fw-semibold px-3" data-bs-toggle="modal" data-bs-target="#exportModal">
                <i class="bi bi-box-arrow-up me-1"></i>Export
            </button>
            <button type="button" class="btn btn-light btn-sm fw-semibold px-3 text-success" data-bs-toggle="modal" data-bs-target="#subjectModal">
                <i class="bi bi-plus-circle me-1"></i>Add New Subject
            </button>
        </div>
    </div>

    <div class="card-body p-0">
        <div id="no-results" class="text-center text-muted py-3" style="display:none;"><i class="bi bi-search me-1"></i>No subjects found.</div>
        @if($subjects->isEmpty())
            <div class="empty-state"><i class="bi bi-book"></i>No subjects added yet. Click "Add New Subject" to add one!</div>
        @else
        <div class="table-responsive" style="max-height:calc(100vh - 210px);overflow-y:auto;">
            <table class="table table-hover table-bordered mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="text-center" style="width:44px;">#</th>
                        <th>Code</th>
                        <th>Subject Name</th>
                        <th>Description</th>
                        <th>Category</th>
                        <th class="text-center" style="width:90px;">Active</th>
                        <th class="text-center" style="width:110px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="subjectTableBody">
                    @foreach($subjects as $i => $sub)
                    <tr>
                        <td class="text-center text-muted small">{{ $i+1 }}</td>
                        <td><span class="badge-code col-code" style="background:#d1fae5;color:#065f46;">{{ $sub->subject_code }}</span></td>
                        <td class="fw-semibold col-name">{{ $sub->subject_name }}</td>
                        <td class="text-muted small">{{ $sub->description ?? '—' }}</td>
                        <td>
                            @if($sub->category)
                                <span class="badge rounded-pill" style="background:#fef3c7;color:#92400e;font-size:.75rem;">{{ $sub->category }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($sub->is_active)
                                <span class="badge rounded-pill" style="background:#d1fae5;color:#065f46;">Active</span>
                            @else
                                <span class="badge rounded-pill" style="background:#fee2e2;color:#991b1b;">Inactive</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-warning btn-action me-1 edit-btn" title="Edit"
                                data-id="{{ $sub->id }}"
                                data-subject_code="{{ $sub->subject_code }}"
                                data-subject_name="{{ $sub->subject_name }}"
                                data-description="{{ $sub->description }}"
                                data-category="{{ $sub->category }}"
                                data-is_active="{{ $sub->is_active ? '1' : '0' }}"
                                data-bs-toggle="modal" data-bs-target="#subjectModal">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-action delete-trigger"
                                data-name="{{ $sub->subject_name }}" data-form="del-sub-{{ $sub->id }}">
                                <i class="bi bi-trash"></i>
                            </button>
                            <form id="del-sub-{{ $sub->id }}" action="{{ route('subjects.destroy',$sub->id) }}" method="POST" class="d-none">
                                @csrf @method('DELETE')
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

{{-- ══ SUBJECT MODAL ══ --}}
<div class="modal fade" id="subjectModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header text-white" id="subModalHeader" style="background:#10b981;">
        <h5 class="modal-title" id="subjectModalLabel"><i class="bi bi-plus-circle me-2"></i>Add New Subject</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="subjectForm" method="POST" novalidate>
        @csrf <span id="subMethodField"></span>
        <div class="modal-body p-4">
          @if($errors->any())
            <div class="alert alert-danger rounded-3 mb-3"><ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li style="font-size:.85rem;">{{$e}}</li>@endforeach</ul></div>
          @endif
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label"><i class="bi bi-hash me-1 text-success"></i>Subject Code</label>
              <input type="text" id="subject_code" name="subject_code"
                class="form-control @error('subject_code') is-invalid @enderror"
                placeholder="e.g. CS101" value="{{ old('subject_code') }}" maxlength="50">
              @error('subject_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label"><i class="bi bi-book me-1 text-success"></i>Subject Name</label>
              <input type="text" id="subject_name" name="subject_name"
                class="form-control @error('subject_name') is-invalid @enderror"
                placeholder="e.g. Computer Science" value="{{ old('subject_name') }}" maxlength="255">
              @error('subject_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-6">
              <label class="form-label"><i class="bi bi-tag me-1 text-success"></i>Category <span class="text-muted">(optional)</span></label>
              <input type="text" id="category" name="category" class="form-control"
                placeholder="e.g. Science, Arts, Commerce..." value="{{ old('category') }}" maxlength="100">
            </div>
            <div class="col-md-6">
              <label class="form-label"><i class="bi bi-toggle-on me-1 text-success"></i>Status</label>
              <div class="d-flex align-items-center gap-3 mt-2">
                <div class="form-check form-switch">
                  <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                    {{ old('is_active', '1') == '1' ? 'checked' : '' }}>
                  <label class="form-check-label" for="is_active">Active Subject</label>
                </div>
              </div>
            </div>
            <div class="col-12">
              <label class="form-label"><i class="bi bi-card-text me-1 text-success"></i>Description <span class="text-muted">(optional)</span></label>
              <textarea id="description" name="description" rows="3"
                class="form-control @error('description') is-invalid @enderror"
                placeholder="Brief description..." maxlength="500">{{ old('description') }}</textarea>
              @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle me-1"></i>Cancel</button>
          <button type="submit" class="btn fw-semibold text-white" id="subSubmitBtn" style="background:#10b981;"><i class="bi bi-plus-circle me-1"></i>Add Subject</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ══ EXPORT MODAL ══ --}}
<div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" style="max-width:1150px;">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header text-white pb-2" style="background:#111827;">
        <div><h5 class="modal-title mb-0 fw-semibold"><i class="bi bi-box-arrow-up me-2"></i>Export Subjects Data</h5>
          <small class="opacity-60" style="font-size:.75rem;">Advanced filters · Column selection</small></div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-0">
        <div class="p-3 border-bottom" style="background:#f8faff;">
          {{-- Dropdowns + sort --}}
          <div class="export-filter-grid mb-2">
            <select id="expCategory" class="form-select form-select-sm export-filter-control"><option value="">All Categories</option>@foreach($subjects->pluck('category')->filter()->unique()->sort() as $category)<option value="{{ $category }}">{{ $category }}</option>@endforeach</select>
            <select id="expStatus" class="form-select form-select-sm export-filter-control"><option value="">All Status</option><option value="1">Active</option><option value="0">Inactive</option></select>
            <select id="expSortBy" class="form-select form-select-sm export-filter-control"><option value="subject_name">Sort: Name</option><option value="subject_code">Sort: Code</option><option value="created_at">Sort: Date Added</option><option value="category">Sort: Category</option></select>
            <select id="expSortDir" class="form-select form-select-sm export-filter-control"><option value="asc">A-Z</option><option value="desc">Z-A</option></select>
            <input type="text" id="expJoinedRange" class="form-control form-control-sm export-filter-control" placeholder="Added date range" autocomplete="off">
            <input type="hidden" id="expCreatedFrom">
            <input type="hidden" id="expCreatedTo">
          </div>
          <div class="export-actions-row mb-3">
            <span class="text-muted small">Filter by the date range subjects were added.</span>
            <div class="export-actions">
                <span class="badge bg-primary rounded-pill me-2" id="expMatchCount">0 matched</span>
                <button type="button" class="btn btn-sm btn-outline-danger px-3" id="expClearFilters" style="font-size:.78rem;"><i class="bi bi-x-circle me-1"></i>Clear Filters</button>
            </div>
          </div>
          {{-- Column selection --}}
          <div class="pt-2 border-top d-flex flex-wrap align-items-center gap-2 mb-2">
            <span style="font-size:.75rem;font-weight:600;color:#374151;">Columns:</span>
            @foreach(['subject_code'=>'Code','subject_name'=>'Name','description'=>'Description','category'=>'Category','is_active'=>'Active','created_at'=>'Created'] as $col=>$lbl)
            <label class="exp-col" for="col_{{ $col }}"><input type="checkbox" class="exp-col-check" id="col_{{ $col }}" value="{{ $col }}" checked> {{ $lbl }}</label>
            @endforeach
          </div>
          {{-- Data Table Preview --}}
          <div style="max-height:250px;overflow-y:auto;border-top:1px solid #dee2e6;">
            <table class="table table-hover table-bordered mb-0 align-middle" style="font-size:.82rem;">
              <thead style="position:sticky;top:0;z-index:1;" id="expTableHead">
              </thead>
              <tbody id="expTableBody"></tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="modal-footer" style="background:#f8faff;">
        <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-dark fw-semibold btn-sm" id="expDoExport"><i class="bi bi-download me-1"></i>Export</button>
      </div>
    </div>
  </div>
</div>

<form id="exportHiddenForm" action="{{ route('export.download') }}" method="POST" style="display:none;">
  @csrf
  <input type="hidden" name="data_type" value="subjects">
  <input type="hidden" name="format" id="exportFormatInput" value="xlsx">
  <div id="exportIdsContainer"></div>
</form>

@endsection

@php
$subjectsForJs = $subjects->map(function($s) {
    return [
        'id'           => $s->id,
        'subject_code' => $s->subject_code,
        'subject_name' => $s->subject_name,
        'description'  => $s->description ?? '',
        'category'     => $s->category ?? '',
        'is_active'    => $s->is_active ? '1' : '0',
        'created_at'   => $s->created_at ? $s->created_at->format('Y-m-d') : null,
    ];
});
@endphp

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
.export-filter-grid { display:grid; gap:.5rem; grid-template-columns:repeat(5,minmax(0,1fr)); }
.export-actions-row { display:flex; justify-content:space-between; align-items:center; gap:.5rem; flex-wrap:wrap; }
.export-actions { display:flex; justify-content:flex-end; align-items:center; gap:.5rem; }
.export-filter-control { width:100%; height:31px; min-width:0; }
#expMatchCount { display:inline-flex; align-items:center; justify-content:center; min-height:24px; min-width:86px; }
.flatpickr-calendar { border:0; border-radius:10px; box-shadow:0 16px 45px rgba(15,23,42,.22); overflow:hidden; }
.flatpickr-calendar .flatpickr-months,.flatpickr-calendar .flatpickr-weekdays { background:#020617; }
.flatpickr-calendar .flatpickr-month,.flatpickr-calendar .flatpickr-current-month,.flatpickr-calendar .flatpickr-weekday,.flatpickr-calendar .flatpickr-prev-month,.flatpickr-calendar .flatpickr-next-month { color:#fff; fill:#fff; }
.flatpickr-calendar .flatpickr-day.selected,.flatpickr-calendar .flatpickr-day.startRange,.flatpickr-calendar .flatpickr-day.endRange { background:#020617; border-color:#020617; }
.flatpickr-calendar .flatpickr-day.inRange { background:#c7d2fe; border-color:#c7d2fe; box-shadow:-5px 0 0 #c7d2fe,5px 0 0 #c7d2fe; }
@media (max-width:991.98px){ .export-filter-grid{grid-template-columns:repeat(2,minmax(0,1fr));} .export-actions-row,.export-actions{justify-content:flex-start;} }
</style>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
const allSubjects = Object.values(@json($subjectsForJs));

// ── Subject form logic ──
const subForm = document.getElementById('subjectForm');
const subMethodField = document.getElementById('subMethodField');
const subModalTitle  = document.getElementById('subjectModalLabel');
const subModalHeader = document.getElementById('subModalHeader');
const subSubmitBtn   = document.getElementById('subSubmitBtn');
const subStoreUrl    = "{{ route('subjects.store') }}";

document.querySelectorAll('.edit-btn').forEach(btn=>{
    btn.addEventListener('click',function(){
        subForm.action = `/subjects/${this.dataset.id}`;
        subMethodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
        subModalTitle.innerHTML  = '<i class="bi bi-pencil-square me-2"></i>Edit Subject';
        subModalHeader.style.background = '#f59e0b';
        subSubmitBtn.style.background   = '#f59e0b';
        subSubmitBtn.style.color        = '#000';
        subSubmitBtn.innerHTML = '<i class="bi bi-save me-1"></i>Update Subject';
        document.getElementById('subject_code').value = this.dataset.subject_code;
        document.getElementById('subject_name').value = this.dataset.subject_name;
        document.getElementById('description').value  = this.dataset.description || '';
        document.getElementById('category').value     = this.dataset.category || '';
        document.getElementById('is_active').checked  = this.dataset.is_active === '1';
    });
});

document.getElementById('subjectModal').addEventListener('hidden.bs.modal',function(){
    subForm.action = subStoreUrl; subMethodField.innerHTML = '';
    subModalTitle.innerHTML = '<i class="bi bi-plus-circle me-2"></i>Add New Subject';
    subModalHeader.style.background = '#10b981';
    subSubmitBtn.style.background   = '#10b981'; subSubmitBtn.style.color = '#fff';
    subSubmitBtn.innerHTML = '<i class="bi bi-plus-circle me-1"></i>Add Subject';
    subForm.reset(); document.getElementById('is_active').checked = true;
});

@if($errors->any()) new bootstrap.Modal(document.getElementById('subjectModal')).show(); @endif

// ── Table search ──
const searchInput = document.getElementById('searchInput');
const clearBtn    = document.getElementById('clearSearch');
const noResults   = document.getElementById('no-results');
const countBadge  = document.getElementById('subject-count');
const rows        = document.querySelectorAll('#subjectTableBody tr');
searchInput.addEventListener('input',function(){
    const q=this.value.toLowerCase().trim(); let v=0;
    rows.forEach(row=>{
        const c=row.querySelector('.col-code')?.textContent.toLowerCase()||'';
        const n=row.querySelector('.col-name')?.textContent.toLowerCase()||'';
        row.style.display=(c.includes(q)||n.includes(q))?(v++,''):'none';
    });
    clearBtn.style.display=q?'inline-block':'none';
    noResults.style.display=v===0&&q?'block':'none';
    countBadge.textContent=v+' subject'+(v!==1?'s':'');
});
clearBtn.addEventListener('click',()=>{searchInput.value='';searchInput.dispatchEvent(new Event('input'));searchInput.focus();});

// ── Export Modal ──
let filteredSubs = [...allSubjects];
const EXP_FILTERS = ['expCategory','expStatus','expSortBy','expSortDir','expJoinedRange'];

function getV(id){ const el=document.getElementById(id); return el?el.value:null; }

function applyExpFilters(){
  const cg=getV('expCategory'); const st=getV('expStatus');
  const crF=getV('expCreatedFrom'); const crT=getV('expCreatedTo');

  let result=allSubjects.filter(s=>{
    if(cg&&s.category!==cg) return false;
    if(st&&s.is_active!==st) return false;
    if(crF&&s.created_at&&s.created_at<crF) return false;
    if(crT&&s.created_at&&s.created_at>crT) return false;
    return true;
  });

  const sortBy=getV('expSortBy')||'subject_name';
  const dir=getV('expSortDir')||'asc';
  result.sort((a,b)=>{ const av=String(a[sortBy]||''),bv=String(b[sortBy]||''); return dir==='asc'?av.localeCompare(bv):bv.localeCompare(av); });
  return result;
}

function updateExportStats() {
  filteredSubs = applyExpFilters();
  document.getElementById('expMatchCount').textContent = filteredSubs.length + ' matched';
  const tbody = document.getElementById('expTableBody');
  const thead = document.getElementById('expTableHead');
  
  const selectedCols = [...document.querySelectorAll('.exp-col-check:checked')].map(c=>c.value);
  const colMap = {'subject_code': 'Code', 'subject_name': 'Subject Name', 'description': 'Description', 'category': 'Category', 'is_active': 'Status', 'created_at': 'Created'};
  
  thead.innerHTML = `<tr style="background:#1a1f2e;color:#fff;">${selectedCols.map(c => `<th style="background:#1a1f2e;">${colMap[c]}</th>`).join('')}</tr>`;

  if(filteredSubs.length === 0) {
      tbody.innerHTML = `<tr><td colspan="${selectedCols.length}" class="text-center text-muted py-4">No records match your filters.</td></tr>`;
      return;
  }
  
  tbody.innerHTML = filteredSubs.map(s => {
      let rowHtml = '';
      selectedCols.forEach(c => {
          if(c === 'subject_code') rowHtml += `<td><span style="background:#d1fae5;color:#065f46;padding:2px 7px;border-radius:5px;font-size:.76rem;font-weight:600;">${s.subject_code}</span></td>`;
          else if(c === 'subject_name') rowHtml += `<td class="fw-semibold">${s.subject_name}</td>`;
          else if(c === 'description') rowHtml += `<td class="text-muted" style="font-size:.8rem;">${s.description||'—'}</td>`;
          else if(c === 'category') rowHtml += `<td>${s.category?`<span style="background:#fef3c7;color:#92400e;padding:2px 7px;border-radius:12px;font-size:.74rem;">${s.category}</span>`:'—'}</td>`;
          else if(c === 'is_active') rowHtml += `<td class="text-center"><span style="background:${s.is_active==='1'?'#d1fae5':'#fee2e2'};color:${s.is_active==='1'?'#065f46':'#991b1b'};padding:2px 8px;border-radius:20px;font-size:.74rem;">${s.is_active==='1'?'Active':'Inactive'}</span></td>`;
          else if(c === 'created_at') rowHtml += `<td style="font-size:.76rem;" class="text-muted">${s.created_at||'—'}</td>`;
      });
      return `<tr>${rowHtml}</tr>`;
  }).join('');
}

EXP_FILTERS.forEach(id=>{
  const el=document.getElementById(id); if(!el) return;
  el.addEventListener('input', updateExportStats);
  el.addEventListener('change', updateExportStats);
});
document.querySelectorAll('.exp-col-check').forEach(chk => chk.addEventListener('change', updateExportStats));

document.getElementById('expClearFilters').addEventListener('click',()=>{
  EXP_FILTERS.forEach(id=>{
    const el=document.getElementById(id); if(!el) return;
    if(id==='expSortBy') el.value = el.options[0].value;
    else if(id==='expSortDir') el.value='asc';
    else el.value='';
  });
  document.getElementById('expCreatedFrom').value='';
  document.getElementById('expCreatedTo').value='';
  if (window.subjectAddedRangePicker) window.subjectAddedRangePicker.clear();
  updateExportStats();
});

function doExport() {
  const ids=filteredSubs.map(s=>s.id);
  const cols=[...document.querySelectorAll('.exp-col-check:checked')].map(c=>c.value);
  const sortBy=document.getElementById('expSortBy').value;
  const sortDir=document.getElementById('expSortDir').value;
  document.getElementById('exportFormatInput').value='xlsx';
  const c=document.getElementById('exportIdsContainer'); c.innerHTML='';
  ids.forEach(id=>{const i=document.createElement('input');i.type='hidden';i.name='ids[]';i.value=id;c.appendChild(i);});
  cols.forEach(col=>{const i=document.createElement('input');i.type='hidden';i.name='columns[]';i.value=col;c.appendChild(i);});
  const sb=document.createElement('input');sb.type='hidden';sb.name='sort_by';sb.value=sortBy;c.appendChild(sb);
  const sd=document.createElement('input');sd.type='hidden';sd.name='sort_dir';sd.value=sortDir;c.appendChild(sd);
  document.getElementById('exportHiddenForm').submit();
}

document.getElementById('expDoExport').addEventListener('click',doExport);
document.getElementById('exportModal').addEventListener('show.bs.modal', updateExportStats);

if (window.flatpickr) {
  window.subjectAddedRangePicker = flatpickr('#expJoinedRange', {
    mode: 'range',
    dateFormat: 'Y-m-d',
    onChange: function(selectedDates, dateStr, instance) {
      const from = selectedDates[0] ? instance.formatDate(selectedDates[0], 'Y-m-d') : '';
      const to = selectedDates[1] ? instance.formatDate(selectedDates[1], 'Y-m-d') : from;
      document.getElementById('expCreatedFrom').value = from;
      document.getElementById('expCreatedTo').value = to;
      updateExportStats();
    }
  });
}

// Initialize table on page load
updateExportStats();
</script>
@endpush
