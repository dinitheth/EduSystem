
const allStudents = {};
const allSubjects = {};
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
const EXP_FILTERS=['expGender','expStatus','expSortBy','expSortDir','expLimit','expCreatedFrom','expCreatedTo'];

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
  const limit=parseInt(getV('expLimit')||'0');
  if(limit>0) result=result.slice(0,limit);
  return result;
}

function updateExportStats() {
  try {
    filteredStudents = applyExpFilters();
    document.getElementById('expMatchCount').textContent = filteredStudents.length + ' matched';
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
    else if(id==='expLimit') el.value='0';
    else el.value='';
  });
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

// Initialize table on page load
updateExportStats();
