@extends('layouts.app')
@section('title','Export Center')
@section('page-title','Export Center')
@section('page-subtitle','Generate filtered reports and datasets')

@section('content')

<form id="exportForm" action="{{ route('export.download') }}" method="POST">
@csrf

<div class="export-panel">

  {{-- ══ HEADER ══ --}}
  <div class="ep-header">
    <div class="d-flex align-items-center gap-3">
      <div class="ep-header-icon"><i class="bi bi-box-arrow-up"></i></div>
      <div>
        <h5 class="mb-0 fw-700">Export Center</h5>
        <small class="opacity-75">Generate filtered reports and datasets</small>
      </div>
    </div>
    <div class="d-flex align-items-center gap-3">
      <div id="previewBadge" class="preview-badge" style="display:none;">
        <i class="bi bi-file-earmark-text me-1"></i>
        <span id="previewCount">0</span> records matched
      </div>
    </div>
  </div>

  <div class="ep-body">
    <div class="ep-left">

      {{-- ══ SECTION 1: Dataset ══ --}}
      <div class="ep-section">
        <div class="ep-section-title"><span class="ep-step">1</span> Select Dataset</div>
        <div class="row g-3" id="datasetCards">
          @foreach([
            ['students','people-fill','#6366f1','#eef2ff','Students','Student registrations and profiles',$counts['students']],
            ['teachers','person-workspace','#0ea5e9','#e0f2fe','Teachers','Teacher records and assignments',$counts['teachers']],
            ['subjects','book-fill','#10b981','#d1fae5','Subjects','Subject catalogue and details',$counts['subjects']],
          ] as [$val,$icon,$color,$bg,$label,$desc,$count])
          <div class="col-md-4">
            <label class="dataset-card w-100" for="ds_{{ $val }}" data-type="{{ $val }}">
              <input type="radio" name="data_type" id="ds_{{ $val }}" value="{{ $val }}" class="d-none" {{ $loop->first?'checked':'' }}>
              <div class="dc-top">
                <div class="dc-icon" style="background:{{ $bg }};color:{{ $color }};"><i class="bi bi-{{ $icon }}"></i></div>
                <span class="dc-count" style="background:{{ $bg }};color:{{ $color }};">{{ $count }}</span>
              </div>
              <div class="dc-title">{{ $label }}</div>
              <div class="dc-desc">{{ $desc }}</div>
            </label>
          </div>
          @endforeach
        </div>
      </div>

      {{-- ══ SECTION 2: Filters ══ --}}
      <div class="ep-section">
        <div class="ep-section-title d-flex justify-content-between align-items-center">
          <span><span class="ep-step">2</span> Filters <span class="text-muted fw-normal" style="font-size:.8rem;">(optional)</span></span>
          <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" id="toggleFilters">
            <i class="bi bi-sliders me-1"></i>Advanced Filters
          </button>
        </div>

        {{-- Quick search always visible --}}
        <div class="row g-2 mb-2">
          <div class="col-md-6">
            <div class="input-group input-group-sm">
              <span class="input-group-text"><i class="bi bi-search text-muted"></i></span>
              <input type="text" name="search" id="filterSearch" class="form-control" placeholder="Search by name, ID, code...">
            </div>
          </div>
          <div class="col-md-3">
            <input type="date" name="date_from" class="form-control form-control-sm filter-input" placeholder="From date">
          </div>
          <div class="col-md-3">
            <input type="date" name="date_to" class="form-control form-control-sm filter-input" placeholder="To date">
          </div>
        </div>

        {{-- Advanced filters (collapsible) --}}
        <div id="advancedFilters" style="display:none;">
          {{-- Students filters --}}
          <div class="adv-group" data-for="students">
            <div class="row g-2">
              <div class="col-md-4">
                <select name="status" class="form-select form-select-sm filter-input">
                  <option value="">All Status</option>
                  <option value="Active">Active</option>
                  <option value="Inactive">Inactive</option>
                </select>
              </div>
              <div class="col-md-4">
                <select name="gender" class="form-select form-select-sm filter-input">
                  <option value="">All Genders</option>
                  <option value="Male">Male</option>
                  <option value="Female">Female</option>
                  <option value="Other">Other</option>
                </select>
              </div>
              <div class="col-md-4">
                <select name="subject_id" class="form-select form-select-sm filter-input">
                  <option value="">All Subjects</option>
                  @foreach($subjects as $sub)
                    <option value="{{ $sub->id }}">{{ $sub->subject_name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="col-md-6">
                <div class="d-flex gap-3 mt-1">
                  <div class="form-check form-check-sm">
                    <input class="form-check-input filter-input" type="checkbox" name="has_email" value="1" id="chkEmail">
                    <label class="form-check-label small" for="chkEmail">Has Email</label>
                  </div>
                  <div class="form-check form-check-sm">
                    <input class="form-check-input filter-input" type="checkbox" name="has_phone" value="1" id="chkPhone">
                    <label class="form-check-label small" for="chkPhone">Has Phone</label>
                  </div>
                </div>
              </div>
            </div>
          </div>
          {{-- Teachers filters --}}
          <div class="adv-group" data-for="teachers" style="display:none;">
            <div class="row g-2">
              <div class="col-md-4">
                <input type="text" name="department" class="form-control form-control-sm filter-input" placeholder="Department...">
              </div>
              <div class="col-md-4">
                <select name="employment_status" class="form-select form-select-sm filter-input">
                  <option value="">All Employment Types</option>
                  <option value="Full-time">Full-time</option>
                  <option value="Part-time">Part-time</option>
                  <option value="Contract">Contract</option>
                </select>
              </div>
              <div class="col-md-4">
                <select name="subject_id" class="form-select form-select-sm filter-input">
                  <option value="">All Subjects</option>
                  @foreach($subjects as $sub)
                    <option value="{{ $sub->id }}">{{ $sub->subject_name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
          {{-- Subjects filters --}}
          <div class="adv-group" data-for="subjects" style="display:none;">
            <div class="row g-2">
              <div class="col-md-6">
                <input type="text" name="category" class="form-control form-control-sm filter-input" placeholder="Category...">
              </div>
              <div class="col-md-6">
                <select name="is_active" class="form-select form-select-sm filter-input">
                  <option value="">All</option>
                  <option value="1">Active Only</option>
                  <option value="0">Inactive Only</option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>

      {{-- ══ SECTION 3: Columns ══ --}}
      <div class="ep-section">
        <div class="ep-section-title d-flex justify-content-between align-items-center">
          <span><span class="ep-step">3</span> Select Columns</span>
          <div class="d-flex gap-2">
            <button type="button" class="btn btn-xs btn-outline-primary" id="selectAll">Select All</button>
            <button type="button" class="btn btn-xs btn-outline-secondary" id="clearAll">Clear All</button>
          </div>
        </div>
        <div id="columnGrid" class="col-grid"></div>
      </div>

      {{-- ══ SECTION 4: Sorting ══ --}}
      <div class="ep-section">
        <div class="ep-section-title"><span class="ep-step">4</span> Sorting</div>
        <div class="row g-2">
          <div class="col-md-6">
            <label class="form-label small text-muted mb-1">Sort By</label>
            <select name="sort_by" id="sortBy" class="form-select form-select-sm"></select>
          </div>
          <div class="col-md-6">
            <label class="form-label small text-muted mb-1">Direction</label>
            <select name="sort_dir" class="form-select form-select-sm">
              <option value="asc">Ascending (A to Z)</option>
              <option value="desc">Descending (Z to A)</option>
            </select>
          </div>
        </div>
      </div>

    </div>{{-- /ep-left --}}

    {{-- ══ RIGHT PANEL ══ --}}
    <div class="ep-right">

      {{-- SECTION 5: Format --}}
      <div class="ep-section">
        <div class="ep-section-title"><span class="ep-step">5</span> Format</div>
        <div class="d-flex flex-column gap-2">
          <label class="fmt-card" for="fmt_csv">
            <input type="radio" name="format" id="fmt_csv" value="csv" class="d-none" checked>
            <i class="bi bi-filetype-csv" style="font-size:1.4rem;color:#6b7280;"></i>
            <div>
              <div class="fw-semibold" style="font-size:.85rem;">CSV</div>
              <div class="text-muted" style="font-size:.72rem;">Universal format</div>
            </div>
          </label>
          <label class="fmt-card" for="fmt_xlsx">
            <input type="radio" name="format" id="fmt_xlsx" value="xlsx" class="d-none">
            <i class="bi bi-file-earmark-excel" style="font-size:1.4rem;color:#16a34a;"></i>
            <div>
              <div class="fw-semibold" style="font-size:.85rem;">Excel (.xlsx)</div>
              <div class="text-muted" style="font-size:.72rem;">Native Excel format</div>
            </div>
          </label>
        </div>
      </div>

      {{-- SECTION 6: Preview Stats --}}
      <div class="ep-section">
        <div class="ep-section-title">Preview</div>
        <div class="stat-row">
          <div class="mini-stat">
            <span class="ms-num" id="statRecords">—</span>
            <span class="ms-label">Records</span>
          </div>
          <div class="mini-stat">
            <span class="ms-num" id="statColumns">—</span>
            <span class="ms-label">Columns</span>
          </div>
          <div class="mini-stat">
            <span class="ms-num" id="statSize">—</span>
            <span class="ms-label">Est. Size</span>
          </div>
        </div>
        <div id="warningEmpty" class="alert alert-warning py-2 px-3 rounded-3 small mt-2" style="display:none;">
          <i class="bi bi-exclamation-triangle me-1"></i>No records match your filters.
        </div>
        <div id="warningLarge" class="alert alert-warning py-2 px-3 rounded-3 small mt-2" style="display:none;">
          <i class="bi bi-exclamation-triangle me-1"></i>Large dataset. Export may take a moment.
        </div>
      </div>

      {{-- Export History --}}
      @if(count($history))
      <div class="ep-section">
        <div class="ep-section-title">Recent Exports</div>
        <div class="history-list">
          @foreach($history as $h)
          <div class="history-item">
            <div class="hi-icon">
              <i class="bi bi-{{ $h['format']==='CSV'?'filetype-csv':'file-earmark-excel' }}"></i>
            </div>
            <div class="hi-info">
              <div class="hi-name">{{ $h['type'] }} — {{ $h['format'] }}</div>
              <div class="hi-meta">{{ $h['count'] }} records &bull; {{ $h['time'] }}</div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
      @endif

      {{-- Generate Button --}}
      <div class="ep-section mt-auto">
        <button type="submit" class="btn btn-generate w-100" id="generateBtn">
          <span id="btnNormal"><i class="bi bi-download me-2"></i>Generate Export</span>
          <span id="btnLoading" style="display:none;">
            <span class="spinner-border spinner-border-sm me-2"></span>Generating...
          </span>
        </button>
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary w-100 mt-2" style="font-size:.85rem;">
          <i class="bi bi-x-circle me-1"></i>Cancel
        </a>
      </div>

    </div>{{-- /ep-right --}}
  </div>{{-- /ep-body --}}
</div>{{-- /export-panel --}}

</form>

@endsection

@push('scripts')
<style>
/* ── Export Panel Shell ── */
.export-panel { background:#fff; border-radius:16px; box-shadow:0 2px 16px rgba(0,0,0,.07); overflow:hidden; }
.ep-header { background:linear-gradient(135deg,#6366f1,#818cf8); color:#fff; padding:20px 28px; display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; }
.ep-header-icon { width:44px;height:44px;background:rgba(255,255,255,.18);border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.3rem; }
.ep-body { display:grid; grid-template-columns:1fr 300px; min-height:500px; }
.ep-left { padding:24px; border-right:1px solid #f1f5f9; display:flex; flex-direction:column; gap:4px; }
.ep-right { padding:20px; background:#fafbff; display:flex; flex-direction:column; gap:4px; }
.ep-section { padding-bottom:20px; margin-bottom:4px; border-bottom:1px solid #f1f5f9; }
.ep-section:last-child { border-bottom:none; padding-bottom:0; }
.ep-section-title { font-weight:700; font-size:.82rem; text-transform:uppercase; letter-spacing:.6px; color:#6b7280; margin-bottom:12px; display:flex; align-items:center; gap:8px; }
.ep-step { display:inline-flex;align-items:center;justify-content:center;width:20px;height:20px;background:#6366f1;color:#fff;border-radius:50%;font-size:.7rem;font-weight:700; }

/* ── Dataset Cards ── */
.dataset-card { border:2px solid #e5e7eb; border-radius:14px; padding:16px; cursor:pointer; transition:all .18s; display:block; background:#fff; }
.dataset-card:hover { border-color:#a5b4fc; background:#f5f7ff; }
.dataset-card:has(input:checked) { border-color:#6366f1; background:#eef2ff; box-shadow:0 0 0 3px rgba(99,102,241,.15); }
.dc-top { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:10px; }
.dc-icon { width:40px;height:40px;border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:1.2rem; }
.dc-count { font-size:.75rem; font-weight:700; padding:3px 8px; border-radius:20px; }
.dc-title { font-weight:700; font-size:.9rem; color:#111827; }
.dc-desc { font-size:.75rem; color:#6b7280; margin-top:2px; }

/* ── Column Grid ── */
.col-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(160px,1fr)); gap:8px; }
.col-check { display:flex; align-items:center; gap:8px; padding:8px 12px; border:1.5px solid #e5e7eb; border-radius:8px; cursor:pointer; transition:all .15s; font-size:.82rem; font-weight:500; }
.col-check:hover { border-color:#6366f1; background:#f5f7ff; }
.col-check input:checked ~ * { color:#4f46e5; }
.col-check:has(input:checked) { border-color:#6366f1; background:#eef2ff; }
.col-check .form-check-input { margin:0; width:16px;height:16px; cursor:pointer; }

/* ── Format Cards ── */
.fmt-card { display:flex; align-items:center; gap:12px; padding:12px 14px; border:2px solid #e5e7eb; border-radius:10px; cursor:pointer; transition:all .15s; background:#fff; }
.fmt-card:hover { border-color:#a5b4fc; }
.fmt-card:has(input:checked) { border-color:#6366f1; background:#eef2ff; box-shadow:0 0 0 2px rgba(99,102,241,.15); }

/* ── Preview Stats ── */
.stat-row { display:flex; gap:8px; }
.mini-stat { flex:1; background:#f1f5f9; border-radius:10px; padding:10px 8px; text-align:center; }
.ms-num { display:block; font-size:1.2rem; font-weight:700; color:#1a1f2e; line-height:1; }
.ms-label { font-size:.68rem; color:#6b7280; text-transform:uppercase; letter-spacing:.5px; }

/* ── History ── */
.history-list { display:flex; flex-direction:column; gap:6px; }
.history-item { display:flex; align-items:center; gap:10px; padding:8px 10px; background:#fff; border-radius:8px; border:1px solid #f1f5f9; }
.hi-icon { width:30px;height:30px;background:#eef2ff;color:#6366f1;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:.9rem;flex-shrink:0; }
.hi-name { font-size:.8rem; font-weight:600; color:#1a1f2e; }
.hi-meta { font-size:.7rem; color:#9ca3af; }

/* ── Generate Button ── */
.btn-generate { background:linear-gradient(135deg,#6366f1,#818cf8); color:#fff; font-weight:700; padding:12px; border-radius:10px; border:none; font-size:.9rem; transition:all .2s; }
.btn-generate:hover { background:linear-gradient(135deg,#4f46e5,#6366f1); color:#fff; transform:translateY(-1px); box-shadow:0 4px 16px rgba(99,102,241,.4); }
.btn-generate:disabled { opacity:.5; cursor:not-allowed; transform:none; }
.btn-xs { font-size:.72rem; padding:2px 10px; border-radius:20px; }

/* ── Preview Badge ── */
.preview-badge { background:rgba(255,255,255,.2); color:#fff; padding:6px 14px; border-radius:20px; font-size:.82rem; font-weight:600; }

/* ── Advanced Filters ── */
.adv-group { padding:12px; background:#f8faff; border-radius:10px; border:1px solid #e0e7ff; margin-top:8px; }

@media(max-width:992px) { .ep-body { grid-template-columns:1fr; } .ep-right { border-top:1px solid #f1f5f9; } }
</style>

<script>
const COLUMNS = {
  students: [
    {key:'reg_no',     label:'Reg No'},
    {key:'full_name',  label:'Full Name'},
    {key:'email',      label:'Email'},
    {key:'phone',      label:'Phone'},
    {key:'dob',        label:'Date of Birth'},
    {key:'gender',     label:'Gender'},
    {key:'status',     label:'Status'},
    {key:'subjects',   label:'Subjects'},
    {key:'created_at', label:'Registered On'},
  ],
  teachers: [
    {key:'employee_no',       label:'Employee No'},
    {key:'full_name',         label:'Full Name'},
    {key:'email',             label:'Email'},
    {key:'phone',             label:'Phone'},
    {key:'specialization',    label:'Specialization'},
    {key:'department',        label:'Department'},
    {key:'employment_status', label:'Employment Status'},
    {key:'subjects',          label:'Subjects Taught'},
    {key:'created_at',        label:'Added On'},
  ],
  subjects: [
    {key:'subject_code', label:'Subject Code'},
    {key:'subject_name', label:'Subject Name'},
    {key:'description',  label:'Description'},
    {key:'category',     label:'Category'},
    {key:'is_active',    label:'Active'},
    {key:'created_at',   label:'Added On'},
  ],
};

const SORT_FIELDS = {
  students: [{v:'full_name',l:'Full Name'},{v:'reg_no',l:'Reg No'},{v:'created_at',l:'Registration Date'},{v:'status',l:'Status'}],
  teachers: [{v:'full_name',l:'Full Name'},{v:'employee_no',l:'Employee No'},{v:'created_at',l:'Added Date'},{v:'department',l:'Department'}],
  subjects: [{v:'subject_name',l:'Subject Name'},{v:'subject_code',l:'Subject Code'},{v:'created_at',l:'Added Date'}],
};

let currentType = 'students';

function renderColumns(type) {
  const grid = document.getElementById('columnGrid');
  grid.innerHTML = '';
  COLUMNS[type].forEach(col => {
    const label = document.createElement('label');
    label.className = 'col-check';
    label.innerHTML = `<input class="form-check-input" type="checkbox" name="columns[]" value="${col.key}" checked><span>${col.label}</span>`;
    grid.appendChild(label);
    label.querySelector('input').addEventListener('change', updatePreview);
  });
  updateColumnCount();
}

function renderSortFields(type) {
  const sel = document.getElementById('sortBy');
  sel.innerHTML = SORT_FIELDS[type].map(f=>`<option value="${f.v}">${f.l}</option>`).join('');
}

function updateColumnCount() {
  const count = document.querySelectorAll('input[name="columns[]"]:checked').length;
  document.getElementById('statColumns').textContent = count || '—';
}

let previewTimer;
function updatePreview() {
  updateColumnCount();
  clearTimeout(previewTimer);
  previewTimer = setTimeout(fetchPreview, 400);
}

function fetchPreview() {
  const fd = new FormData(document.getElementById('exportForm'));
  fd.append('data_type', currentType);

  fetch("{{ route('export.preview') }}", {
    method: 'POST',
    headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json'},
    body: fd
  })
  .then(r => r.json())
  .then(data => {
    const n = data.count;
    document.getElementById('statRecords').textContent  = n;
    document.getElementById('previewBadge').style.display = '';
    document.getElementById('previewCount').textContent = n;

    const cols = document.querySelectorAll('input[name="columns[]"]:checked').length || 1;
    const est  = Math.round((n * cols * 12) / 1024);
    document.getElementById('statSize').textContent = est < 1024 ? est+'KB' : (est/1024).toFixed(1)+'MB';

    document.getElementById('warningEmpty').style.display = n === 0 ? '' : 'none';
    document.getElementById('warningLarge').style.display = n > 1000 ? '' : 'none';
    document.getElementById('generateBtn').disabled = n === 0;
  })
  .catch(() => {});
}

function switchDataset(type) {
  currentType = type;
  renderColumns(type);
  renderSortFields(type);

  // Show correct advanced filters group
  document.querySelectorAll('.adv-group').forEach(g => {
    g.style.display = g.dataset.for === type ? '' : 'none';
  });

  updatePreview();
}

// Init
document.querySelectorAll('.dataset-card').forEach(card => {
  card.addEventListener('click', function() {
    switchDataset(this.dataset.type);
  });
});

document.getElementById('toggleFilters').addEventListener('click', function() {
  const af = document.getElementById('advancedFilters');
  af.style.display = af.style.display === 'none' ? '' : 'none';
  this.innerHTML = af.style.display === 'none'
    ? '<i class="bi bi-sliders me-1"></i>Advanced Filters'
    : '<i class="bi bi-sliders me-1"></i>Hide Filters';
});

document.getElementById('selectAll').addEventListener('click', function() {
  document.querySelectorAll('input[name="columns[]"]').forEach(c => c.checked = true);
  updatePreview();
});
document.getElementById('clearAll').addEventListener('click', function() {
  document.querySelectorAll('input[name="columns[]"]').forEach(c => c.checked = false);
  updatePreview();
});

document.getElementById('exportForm').querySelectorAll('.filter-input, input[name="search"]').forEach(el => {
  el.addEventListener('change', updatePreview);
  el.addEventListener('input',  updatePreview);
});

document.getElementById('exportForm').addEventListener('submit', function() {
  document.getElementById('btnNormal').style.display  = 'none';
  document.getElementById('btnLoading').style.display = '';
  document.getElementById('generateBtn').disabled     = true;
  setTimeout(() => {
    document.getElementById('btnNormal').style.display  = '';
    document.getElementById('btnLoading').style.display = 'none';
    document.getElementById('generateBtn').disabled     = false;
  }, 4000);
});

// Boot
switchDataset('students');
</script>
@endpush
