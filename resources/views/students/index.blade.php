@extends('layouts.app')
@section('title','Students')
@section('page-title','Students')
@section('page-subtitle','Manage student registrations')

@section('content')

<div class="card">
    <div class="card-header bg-dark text-white py-3 d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <span><i class="bi bi-people-fill me-2"></i>All Registered Students
            <span class="badge bg-primary rounded-pill ms-2" id="student-count">{{ $students->count() }} students</span>
        </span>
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <div class="input-group" style="width:250px;">
                <span class="input-group-text bg-white border-end-0 py-1"><i class="bi bi-search text-muted small"></i></span>
                <input type="text" id="searchInput" class="form-control border-start-0 ps-0 py-1"
                    placeholder="Search Reg No, Name, Email..." style="font-size:.85rem;" autocomplete="off">
                <button class="btn btn-outline-secondary btn-sm" id="clearSearch" style="display:none;"><i class="bi bi-x-lg"></i></button>
            </div>
            <button type="button" class="btn btn-sm btn-outline-light fw-semibold px-3" data-bs-toggle="modal" data-bs-target="#exportModal">
                <i class="bi bi-box-arrow-up me-1"></i>Export
            </button>
            <button type="button" class="btn btn-sm btn-primary fw-semibold px-3" data-bs-toggle="modal" data-bs-target="#studentModal">
                <i class="bi bi-person-plus-fill me-1"></i>Register New Student
            </button>
        </div>
    </div>

    <div class="card-body p-0">
        <div id="no-results" class="text-center text-muted py-3" style="display:none;"><i class="bi bi-search me-1"></i>No students found.</div>
        @if($students->isEmpty())
            <div class="empty-state"><i class="bi bi-inbox"></i>No students registered yet.</div>
        @else
        <div class="table-responsive" style="max-height:calc(100vh - 210px);overflow-y:auto;">
            <table class="table table-hover table-bordered mb-0 align-middle">
                <thead>
                    <tr>
                        <th class="text-center" style="width:44px;">#</th>
                        <th>Reg No</th><th>Full Name</th><th>Email</th><th>Phone</th>
                        <th>Date of Birth</th><th>Gender</th><th>Status</th><th>Subjects</th>
                        <th class="text-center" style="width:100px;">Actions</th>
                    </tr>
                </thead>
                <tbody id="studentTableBody">
                    @foreach($students as $i => $s)
                    <tr>
                        <td class="text-center text-muted small">{{ $i+1 }}</td>
                        <td><span class="badge-code col-reg-no">{{ $s->reg_no }}</span></td>
                        <td class="fw-semibold col-full-name">{{ $s->full_name }}</td>
                        <td class="text-muted small col-email">{{ $s->email }}</td>
                        <td>{{ $s->phone }}</td>
                        <td>{{ $s->dob ? \Carbon\Carbon::parse($s->dob)->format('d M Y') : '—' }}</td>
                        <td>@if($s->gender)<span class="badge rounded-pill" style="background:#f1f5f9;color:#475569;">{{ $s->gender }}</span>@else<span class="text-muted">—</span>@endif</td>
                        <td>
                            <span class="badge rounded-pill" style="background:{{ $s->status==='Active'?'#d1fae5':'#fee2e2' }};color:{{ $s->status==='Active'?'#065f46':'#991b1b' }};">
                                {{ $s->status ?? 'Active' }}
                            </span>
                        </td>
                        <td>@foreach($s->subjects as $sub)<span class="badge rounded-pill me-1" style="background:#eef2ff;color:#6366f1;font-size:.7rem;">{{ $sub->subject_name }}</span>@endforeach</td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-warning btn-action me-1 edit-btn" title="Edit"
                                data-id="{{ $s->id }}" data-reg_no="{{ $s->reg_no }}" data-full_name="{{ $s->full_name }}"
                                data-email="{{ $s->email }}" data-phone="{{ $s->phone }}" data-dob="{{ $s->dob }}"
                                data-gender="{{ $s->gender }}" data-status="{{ $s->status ?? 'Active' }}"
                                data-subjects="{{ $s->subjects->pluck('id')->join(',') }}"
                                data-bs-toggle="modal" data-bs-target="#studentModal"><i class="bi bi-pencil"></i></button>
                            <button type="button" class="btn btn-sm btn-outline-danger btn-action delete-trigger"
                                data-name="{{ $s->full_name }}" data-form="del-s-{{ $s->id }}"><i class="bi bi-trash"></i></button>
                            <form id="del-s-{{ $s->id }}" action="{{ route('students.destroy',$s->id) }}" method="POST" class="d-none">@csrf @method('DELETE')</form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>

{{-- ══ STUDENT MODAL ══ --}}
<div class="modal fade" id="studentModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-xl">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header text-white" id="sModalHeader" style="background:#6366f1;">
        <h5 class="modal-title" id="studentModalLabel"><i class="bi bi-person-plus-fill me-2"></i>Register New Student</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="studentForm" method="POST" novalidate>
        @csrf <span id="sMethodField"></span>
        <div class="modal-body p-4">
          @if($errors->any())<div class="alert alert-danger rounded-3 mb-3"><ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li style="font-size:.85rem;">{{$e}}</li>@endforeach</ul></div>@endif
          <div class="row g-3">
            <div class="col-md-6">
              <div class="row g-3">
                <div class="col-6"><label class="form-label"><i class="bi bi-hash me-1 text-primary"></i>Reg No</label>
                  <input type="text" id="reg_no" name="reg_no" class="form-control @error('reg_no') is-invalid @enderror" placeholder="e.g. REG2024001" value="{{ old('reg_no') }}">
                  @error('reg_no')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-6"><label class="form-label"><i class="bi bi-person me-1 text-primary"></i>Full Name</label>
                  <input type="text" id="full_name" name="full_name" class="form-control @error('full_name') is-invalid @enderror" placeholder="e.g. Kasun Perera" value="{{ old('full_name') }}">
                  @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-12"><label class="form-label"><i class="bi bi-envelope me-1 text-primary"></i>Email</label>
                  <input type="email" id="email" name="email" class="form-control @error('email') is-invalid @enderror" placeholder="e.g. student@gmail.com" value="{{ old('email') }}">
                  @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-6"><label class="form-label"><i class="bi bi-telephone me-1 text-primary"></i>Phone</label>
                  <input type="text" id="phone" name="phone" class="form-control @error('phone') is-invalid @enderror" placeholder="0771234567" value="{{ old('phone') }}">
                  @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-6"><label class="form-label"><i class="bi bi-calendar3 me-1 text-primary"></i>Date of Birth</label>
                  <input type="date" id="dob" name="dob" class="form-control @error('dob') is-invalid @enderror" value="{{ old('dob') }}" max="{{ date('Y-m-d',strtotime('-1 day')) }}">
                  @error('dob')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
                <div class="col-6"><label class="form-label"><i class="bi bi-gender-ambiguous me-1 text-primary"></i>Gender</label>
                  <select id="gender" name="gender" class="form-select">
                    <option value="">Select...</option>
                    <option value="Male" {{ old('gender')=='Male'?'selected':'' }}>Male</option>
                    <option value="Female" {{ old('gender')=='Female'?'selected':'' }}>Female</option>
                    <option value="Other" {{ old('gender')=='Other'?'selected':'' }}>Other</option>
                  </select></div>
                <div class="col-6"><label class="form-label"><i class="bi bi-toggle-on me-1 text-primary"></i>Status</label>
                  <select id="status" name="status" class="form-select">
                    <option value="Active" {{ old('status','Active')=='Active'?'selected':'' }}>Active</option>
                    <option value="Inactive" {{ old('status')=='Inactive'?'selected':'' }}>Inactive</option>
                  </select></div>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label"><i class="bi bi-book me-1 text-primary"></i>Assign Subjects</label>
              <div class="border rounded-3 p-3" style="background:#f9fafb;">
                <div class="input-group mb-2">
                  <span class="input-group-text bg-white"><i class="bi bi-search text-muted small"></i></span>
                  <input type="text" id="subjectSearch" class="form-control" placeholder="Search by name or code...">
                </div>
                <div id="subjectDropdown" class="border rounded-3 bg-white mb-2" style="max-height:160px;overflow-y:auto;display:none;"></div>
                <div id="selectedSubjects" class="d-flex flex-wrap gap-1 mt-1" style="min-height:36px;">
                  <span class="text-muted small" id="noSubjectMsg">No subjects assigned yet</span>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer border-0 pt-0">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><i class="bi bi-x-circle me-1"></i>Cancel</button>
          <button type="submit" class="btn btn-primary fw-semibold" id="sSubmitBtn"><i class="bi bi-person-check me-1"></i>Register Student</button>
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
        <div><h5 class="modal-title mb-0 fw-semibold"><i class="bi bi-box-arrow-up me-2"></i>Export Students Data</h5>
          <small class="opacity-60" style="font-size:.75rem;">Advanced filters · Column selection · Export scope</small></div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-0">
        <div class="p-3 border-bottom" style="background:#f8faff;">
          {{-- Dropdowns + sort --}}
          <div class="export-filter-grid mb-2">
            <select id="expGender" class="form-select form-select-sm export-filter-control"><option value="">All Genders</option><option>Male</option><option>Female</option><option>Other</option></select>
            <select id="expStatus" class="form-select form-select-sm export-filter-control"><option value="">All Status</option><option>Active</option><option>Inactive</option></select>
            <select id="expSortBy" class="form-select form-select-sm export-filter-control"><option value="full_name">Sort: Name</option><option value="reg_no">Sort: Reg No</option><option value="created_at">Sort: Date Added</option><option value="dob">Sort: DOB</option><option value="status">Sort: Status</option></select>
            <select id="expSortDir" class="form-select form-select-sm export-filter-control"><option value="asc">A-Z</option><option value="desc">Z-A</option></select>
            <input type="text" id="expJoinedRange" class="form-control form-control-sm export-filter-control" placeholder="Joined date range" autocomplete="off">
            <input type="hidden" id="expCreatedFrom">
            <input type="hidden" id="expCreatedTo">
          </div>
          <div class="export-actions-row mb-3">
            <span class="text-muted small">Filter by the date range students joined.</span>
            <div class="export-actions">
                <span class="badge bg-primary rounded-pill me-2" id="expMatchCount">{{ $students->count() }} matched</span>
                <button type="button" class="btn btn-sm btn-outline-danger px-3" id="expClearFilters" style="font-size:.78rem;"><i class="bi bi-x-circle me-1"></i>Clear Filters</button>
            </div>
          </div>
          {{-- Column selection --}}
          <div class="pt-2 border-top d-flex flex-wrap align-items-center gap-2 mb-2">
            <span style="font-size:.75rem;font-weight:600;color:#374151;">Columns:</span>
            @foreach(['reg_no'=>'Reg No','full_name'=>'Name','email'=>'Email','phone'=>'Phone','dob'=>'DOB','gender'=>'Gender','status'=>'Status','subjects'=>'Subjects','created_at'=>'Created'] as $col=>$lbl)
            <label class="exp-col" for="col_{{ $col }}"><input type="checkbox" class="exp-col-check" id="col_{{ $col }}" value="{{ $col }}" checked {{ in_array($col, ['reg_no', 'full_name']) ? 'disabled' : '' }}> {{ $lbl }}</label>
            @endforeach
          </div>
          {{-- Data Table Preview --}}
          <div class="mt-3 table-responsive" style="max-height:250px;border:1px solid #e5e7eb;border-radius:6px;">
            <table class="table table-hover table-bordered mb-0 align-middle" style="font-size:.82rem;">
              <thead style="position:sticky;top:0;z-index:1;" id="expTableHead">
                <tr style="background:#1a1f2e;color:#fff;">
                  <th style="background:#1a1f2e;">Reg No</th>
                  <th style="background:#1a1f2e;">Name</th>
                  <th style="background:#1a1f2e;">Email</th>
                  <th style="background:#1a1f2e;">Phone</th>
                  <th style="background:#1a1f2e;">DOB</th>
                  <th style="background:#1a1f2e;">Gender</th>
                  <th style="background:#1a1f2e;">Status</th>
                  <th style="background:#1a1f2e;">Subjects</th>
                  <th style="background:#1a1f2e;">Joined</th>
                </tr>
              </thead>
              <tbody id="expTableBody">
                @forelse($students as $s)
                <tr>
                  <td><span style="background:#e0f2fe;color:#0369a1;padding:2px 7px;border-radius:5px;font-size:.76rem;font-weight:600;">{{ $s->reg_no }}</span></td>
                  <td class="fw-semibold">{{ $s->full_name }}</td>
                  <td class="text-muted">{{ $s->email ?: '—' }}</td>
                  <td>{{ $s->phone ?: '—' }}</td>
                  <td>{{ $s->dob ?: '—' }}</td>
                  <td>{{ $s->gender ?: '—' }}</td>
                  <td><span style="background:{{ ($s->status ?? 'Active') === 'Active' ? '#d1fae5' : '#fee2e2' }};color:{{ ($s->status ?? 'Active') === 'Active' ? '#065f46' : '#991b1b' }};padding:2px 8px;border-radius:20px;font-size:.74rem;">{{ $s->status ?? 'Active' }}</span></td>
                  <td style="font-size:.76rem;">{{ $s->subjects->pluck('subject_name')->join(', ') ?: '—' }}</td>
                  <td style="font-size:.76rem;" class="text-muted">{{ $s->created_at ? $s->created_at->format('Y-m-d') : '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center text-muted py-4">No students found.</td></tr>
                @endforelse
              </tbody>
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

{{-- Hidden export form --}}
<form id="exportHiddenForm" action="{{ route('export.download') }}" method="POST" style="display:none;">
  @csrf
  <input type="hidden" name="data_type" value="students">
  <input type="hidden" name="format" id="exportFormatInput" value="xlsx">
  <div id="exportIdsContainer"></div>
</form>

@endsection

@php
$studentsForJs = $students->map(function($s) {
    return [
        'id'         => $s->id,
        'reg_no'     => $s->reg_no,
        'full_name'  => $s->full_name,
        'email'      => $s->email ?? '',
        'phone'      => $s->phone ?? '',
        'dob'        => $s->dob ?? '',
        'gender'     => $s->gender ?? '',
        'status'     => $s->status ?? 'Active',
        'subjects'   => $s->subjects->pluck('subject_name')->join(', '),
        'created_at' => $s->created_at ? $s->created_at->format('Y-m-d') : '',
        'updated_at' => $s->updated_at ? $s->updated_at->format('Y-m-d') : '',
    ];
});
$subjectsForJs = $subjects->map(function($s) {
    return ['id' => $s->id, 'subject_name' => $s->subject_name, 'subject_code' => $s->subject_code];
});
@endphp

@push('scripts')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
.export-filter-grid {
    display: grid;
    gap: .5rem;
    grid-template-columns: repeat(5, minmax(0, 1fr));
}
.export-actions-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: .5rem;
    flex-wrap: wrap;
}
.export-actions {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: .5rem;
}
.export-filter-control {
    width: 100%;
    height: 31px;
    min-width: 0;
}
#expMatchCount {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 24px;
    min-width: 86px;
}
.flatpickr-calendar {
    border: 0;
    border-radius: 10px;
    box-shadow: 0 16px 45px rgba(15, 23, 42, .22);
    overflow: hidden;
}
.flatpickr-calendar .flatpickr-months,
.flatpickr-calendar .flatpickr-weekdays {
    background: #020617;
}
.flatpickr-calendar .flatpickr-month,
.flatpickr-calendar .flatpickr-current-month,
.flatpickr-calendar .flatpickr-weekday,
.flatpickr-calendar .flatpickr-prev-month,
.flatpickr-calendar .flatpickr-next-month {
    color: #fff;
    fill: #fff;
}
.flatpickr-calendar .flatpickr-day.selected,
.flatpickr-calendar .flatpickr-day.startRange,
.flatpickr-calendar .flatpickr-day.endRange {
    background: #020617;
    border-color: #020617;
}
.flatpickr-calendar .flatpickr-day.inRange {
    background: #c7d2fe;
    border-color: #c7d2fe;
    box-shadow: -5px 0 0 #c7d2fe, 5px 0 0 #c7d2fe;
}
@media (max-width: 991.98px) {
    .export-filter-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    .export-actions-row,
    .export-actions {
        justify-content: flex-start;
    }
}
</style>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
const allStudents = Object.values(@json($studentsForJs));
const allSubjects = Object.values(@json($subjectsForJs));
let selectedSubjectIds = new Set();

const sForm        = document.getElementById('studentForm');
const sMethodField = document.getElementById('sMethodField');
const sModalTitle  = document.getElementById('studentModalLabel');
const sModalHeader = document.getElementById('sModalHeader');
const sSubmitBtn   = document.getElementById('sSubmitBtn');
const sStoreUrl    = "{{ route('students.store') }}";

function renderSubjectTags() {
    const c = document.getElementById('selectedSubjects');
    const n = document.getElementById('noSubjectMsg');
    c.querySelectorAll('.sub-tag').forEach(e=>e.remove());
    sForm.querySelectorAll('input[name="subject_ids[]"]').forEach(e=>e.remove());
    if(!selectedSubjectIds.size){n.style.display='';return;}
    n.style.display='none';
    selectedSubjectIds.forEach(id=>{
        const sub=allSubjects.find(s=>s.id==id); if(!sub)return;
        const tag=document.createElement('span');
        tag.className='sub-tag badge d-flex align-items-center gap-1';
        tag.style.cssText='background:#eef2ff;color:#6366f1;font-size:.8rem;padding:5px 8px;border-radius:8px;';
        tag.innerHTML=`${sub.subject_name} <i class="bi bi-x-circle" style="cursor:pointer;" data-id="${id}"></i>`;
        tag.querySelector('i').addEventListener('click',function(){selectedSubjectIds.delete(parseInt(this.dataset.id));renderSubjectTags();});
        c.appendChild(tag);
        const h=document.createElement('input');h.type='hidden';h.name='subject_ids[]';h.value=id;sForm.appendChild(h);
    });
}

document.getElementById('subjectSearch').addEventListener('input',function(){
    const q=this.value.toLowerCase().trim();
    const dd=document.getElementById('subjectDropdown');
    if(!q){dd.style.display='none';dd.innerHTML='';return;}
    const m=allSubjects.filter(s=>s.subject_name.toLowerCase().includes(q)||s.subject_code.toLowerCase().includes(q));
    dd.innerHTML=m.length===0?'<div class="px-3 py-2 text-muted small">No subjects found</div>'
        :m.map(s=>`<div class="px-3 py-2 subject-option" data-id="${s.id}" style="cursor:pointer;font-size:.85rem;border-bottom:1px solid #f1f5f9;">
            <span style="background:#eef2ff;color:#6366f1;padding:2px 6px;border-radius:4px;font-size:.72rem;">${s.subject_code}</span>
            <span class="ms-2">${s.subject_name}</span>
            ${selectedSubjectIds.has(s.id)?'<i class="bi bi-check-circle-fill text-success float-end mt-1"></i>':''}
        </div>`).join('');
    dd.querySelectorAll('.subject-option').forEach(el=>{
        el.addEventListener('click',function(){
            const id=parseInt(this.dataset.id);
            if(selectedSubjectIds.has(id))selectedSubjectIds.delete(id); else selectedSubjectIds.add(id);
            renderSubjectTags();
            document.getElementById('subjectSearch').dispatchEvent(new Event('input'));
        });
        el.addEventListener('mouseenter',function(){this.style.background='#f8faff';});
        el.addEventListener('mouseleave',function(){this.style.background='';});
    });
    dd.style.display='block';
});

document.querySelectorAll('.edit-btn').forEach(btn=>{
    btn.addEventListener('click',function(){
        sForm.action=`/students/${this.dataset.id}`;
        sMethodField.innerHTML='<input type="hidden" name="_method" value="PUT">';
        sModalTitle.innerHTML='<i class="bi bi-pencil-square me-2"></i>Edit Student';
        sModalHeader.style.background='#f59e0b';
        sSubmitBtn.className='btn btn-warning fw-semibold';
        sSubmitBtn.innerHTML='<i class="bi bi-save me-1"></i>Update Student';
        document.getElementById('reg_no').value=this.dataset.reg_no;
        document.getElementById('full_name').value=this.dataset.full_name;
        document.getElementById('email').value=this.dataset.email;
        document.getElementById('phone').value=this.dataset.phone;
        document.getElementById('dob').value=this.dataset.dob;
        document.getElementById('gender').value=this.dataset.gender||'';
        document.getElementById('status').value=this.dataset.status||'Active';
        selectedSubjectIds=new Set();
        if(this.dataset.subjects) this.dataset.subjects.split(',').forEach(id=>{if(id)selectedSubjectIds.add(parseInt(id));});
        renderSubjectTags();
    });
});

document.getElementById('studentModal').addEventListener('hidden.bs.modal',function(){
    sForm.action=sStoreUrl; sMethodField.innerHTML='';
    sModalTitle.innerHTML='<i class="bi bi-person-plus-fill me-2"></i>Register New Student';
    sModalHeader.style.background='#6366f1';
    sSubmitBtn.className='btn btn-primary fw-semibold';
    sSubmitBtn.innerHTML='<i class="bi bi-person-check me-1"></i>Register Student';
    sForm.reset(); selectedSubjectIds=new Set(); renderSubjectTags();
    document.getElementById('subjectDropdown').style.display='none';
});

document.addEventListener('click',function(e){
    if(!e.target.closest('#subjectSearch')&&!e.target.closest('#subjectDropdown'))
        document.getElementById('subjectDropdown').style.display='none';
});

@if($errors->any()) new bootstrap.Modal(document.getElementById('studentModal')).show(); @endif

// ─── Main table search ───
const searchInput=document.getElementById('searchInput');
const clearBtn=document.getElementById('clearSearch');
const noResults=document.getElementById('no-results');
const countBadge=document.getElementById('student-count');
const rows=document.querySelectorAll('#studentTableBody tr');
searchInput.addEventListener('input',function(){
    const q=this.value.toLowerCase().trim(); let v=0;
    rows.forEach(row=>{
        const rn=row.querySelector('.col-reg-no')?.textContent.toLowerCase()||'';
        const fn=row.querySelector('.col-full-name')?.textContent.toLowerCase()||'';
        const em=row.querySelector('.col-email')?.textContent.toLowerCase()||'';
        row.style.display=(rn.includes(q)||fn.includes(q)||em.includes(q))?(v++,''):'none';
    });
    clearBtn.style.display=q?'inline-block':'none';
    noResults.style.display=v===0&&q?'block':'none';
    countBadge.textContent=v+' student'+(v!==1?'s':'');
});
clearBtn.addEventListener('click',()=>{searchInput.value='';searchInput.dispatchEvent(new Event('input'));searchInput.focus();});

// ─── Export Modal Logic ───
let filteredStudents=[...allStudents];
const EXP_FILTERS=['expGender','expStatus','expSortBy','expSortDir','expJoinedRange'];

function getV(id){ const el=document.getElementById(id); return el?el.value:null; }

function applyExpFilters(){
  const gn=getV('expGender'); const st=getV('expStatus');
  const crF=getV('expCreatedFrom'); const crT=getV('expCreatedTo');

  let result = allStudents.filter(s=>{
    if(gn&&s.gender!==gn) return false;
    if(st&&s.status!==st) return false;
    if(crF&&s.created_at&&s.created_at<crF) return false;
    if(crT&&s.created_at&&s.created_at>crT) return false;
    return true;
  });

  const sortBy=getV('expSortBy')||'full_name';
  const dir=getV('expSortDir')||'asc';
  result.sort((a,b)=>{ const av=String(a[sortBy]||''),bv=String(b[sortBy]||''); return dir==='asc'?av.localeCompare(bv):bv.localeCompare(av); });
  return result;
}

function updateExportStats() {
  try {
    filteredStudents = applyExpFilters();
    document.getElementById('expMatchCount').textContent = filteredStudents.length + " matched";
    const tbody = document.getElementById('expTableBody');
    const thead = document.getElementById('expTableHead');
    
    const selectedCols = [...document.querySelectorAll('.exp-col-check:checked')].map(c=>c.value);
    const colMap = {'reg_no': 'Reg No', 'full_name': 'Name', 'email': 'Email', 'phone': 'Phone', 'dob': 'DOB', 'gender': 'Gender', 'status': 'Status', 'subjects': 'Subjects', 'created_at': 'Joined'};
    
    thead.innerHTML = `<tr style="background:#1a1f2e;color:#fff;">${selectedCols.map(c => `<th style="background:#1a1f2e;">${colMap[c]}</th>`).join('')}</tr>`;

    if(filteredStudents.length === 0) {
        tbody.innerHTML = `<tr><td colspan="${selectedCols.length}" class="text-center text-muted py-4">No records match your filters.</td></tr>`;
        return;
    }
    
    tbody.innerHTML = filteredStudents.map(s => {
        let rowHtml = '';
        selectedCols.forEach(c => {
            if(c === 'reg_no') rowHtml += `<td><span style="background:#e0f2fe;color:#0369a1;padding:2px 7px;border-radius:5px;font-size:.76rem;font-weight:600;">${s.reg_no}</span></td>`;
            else if(c === 'full_name') rowHtml += `<td class="fw-semibold">${s.full_name}</td>`;
            else if(c === 'email') rowHtml += `<td class="text-muted">${s.email||'—'}</td>`;
            else if(c === 'phone') rowHtml += `<td>${s.phone||'—'}</td>`;
            else if(c === 'dob') rowHtml += `<td>${s.dob||'—'}</td>`;
            else if(c === 'gender') rowHtml += `<td>${s.gender||'—'}</td>`;
            else if(c === 'status') rowHtml += `<td><span style="background:${s.status==='Active'?'#d1fae5':'#fee2e2'};color:${s.status==='Active'?'#065f46':'#991b1b'};padding:2px 8px;border-radius:20px;font-size:.74rem;">${s.status}</span></td>`;
            else if(c === 'subjects') rowHtml += `<td style="font-size:.76rem;">${s.subjects||'—'}</td>`;
            else if(c === 'created_at') rowHtml += `<td style="font-size:.76rem;" class="text-muted">${s.created_at||'—'}</td>`;
        });
        return `<tr>${rowHtml}</tr>`;
    }).join('');
  } catch (e) {
    document.getElementById('expMatchCount').textContent = "ERROR: " + e.message;
    document.getElementById('expTableBody').innerHTML = `<tr><td colspan="9" class="text-danger py-4" style="white-space:pre-wrap;">${e.stack}</td></tr>`;
  }
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
  if (window.joinedRangePicker) window.joinedRangePicker.clear();
  updateExportStats();
});

function doExport() {
  const ids=filteredStudents.map(s=>s.id);
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
  window.joinedRangePicker = flatpickr('#expJoinedRange', {
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
