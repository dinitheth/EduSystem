@extends('layouts.app')

@section('title', 'Teachers')
@section('page-title', 'Teachers')
@section('page-subtitle', 'Manage teacher records')

@section('content')

<div class="card">

    <div class="card-header text-white py-3 d-flex flex-wrap gap-2 justify-content-between align-items-center"
         style="background: linear-gradient(135deg,#0ea5e9,#38bdf8);">
        <span><i class="bi bi-person-workspace me-2"></i>All Teachers
            <span class="badge bg-white text-info rounded-pill ms-2" id="teacher-count">
                {{ $teachers->count() }} teacher{{ $teachers->count() !== 1 ? 's' : '' }}
            </span>
        </span>
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <div class="input-group" style="width:280px;">
                <span class="input-group-text bg-white border-end-0 py-1"><i class="bi bi-search text-muted small"></i></span>
                <input type="text" id="searchInput" class="form-control border-start-0 ps-0 py-1"
                    placeholder="Search Emp No, Name, Email..." autocomplete="off" style="font-size:0.85rem;">
                <button class="btn btn-outline-secondary btn-sm" type="button" id="clearSearch" style="display:none;">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <button type="button" class="btn btn-light btn-sm fw-semibold px-3 text-info"
                    data-bs-toggle="modal" data-bs-target="#teacherModal">
                <i class="bi bi-person-plus-fill me-1"></i>Add New Teacher
            </button>
        </div>
    </div>

    <div class="card-body p-0">
        <div id="no-results" class="text-center text-muted py-3" style="display:none;">
            <i class="bi bi-search me-1"></i>No teachers found matching your search.
        </div>
        @if($teachers->isEmpty())
            <div class="empty-state">
                <i class="bi bi-person-workspace"></i>
                No teachers added yet. Click "Add New Teacher" to add one!
            </div>
        @else
            <div class="table-responsive" style="max-height: calc(100vh - 210px); overflow-y: auto;">
                <table class="table table-hover table-bordered mb-0 align-middle">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:48px;">#</th>
                            <th>Emp No</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Specialization</th>
                            <th class="text-center" style="width:110px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="teacherTableBody">
                        @foreach($teachers as $index => $t)
                            <tr>
                                <td class="text-center text-muted small">{{ $index + 1 }}</td>
                                <td><span class="badge-code col-emp-no">{{ $t->employee_no }}</span></td>
                                <td class="fw-semibold col-full-name">{{ $t->full_name }}</td>
                                <td class="text-muted small col-email">{{ $t->email }}</td>
                                <td>{{ $t->phone }}</td>
                                <td>{{ $t->specialization }}</td>
                                <td class="text-center">
                                    <button type="button"
                                        class="btn btn-sm btn-outline-warning btn-action me-1 edit-btn"
                                        title="Edit"
                                        data-id="{{ $t->id }}"
                                        data-employee_no="{{ $t->employee_no }}"
                                        data-full_name="{{ $t->full_name }}"
                                        data-email="{{ $t->email }}"
                                        data-phone="{{ $t->phone }}"
                                        data-specialization="{{ $t->specialization }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#teacherModal">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-danger btn-action delete-trigger"
                                        title="Delete"
                                        data-name="{{ $t->full_name }}"
                                        data-form="delete-teacher-{{ $t->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <form id="delete-teacher-{{ $t->id }}"
                                          action="{{ route('teachers.destroy', $t->id) }}"
                                          method="POST" class="d-none">
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

{{-- ══ MODAL ══ --}}
<div class="modal fade" id="teacherModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header text-white" id="modalHeader" style="background:#0ea5e9;">
                <h5 class="modal-title" id="teacherModalLabel">
                    <i class="bi bi-person-plus-fill me-2"></i>Add New Teacher
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="teacherForm" method="POST" novalidate>
                @csrf
                <span id="methodField"></span>
                <div class="modal-body p-4">

                    @if($errors->any())
                        <div class="alert alert-danger rounded-3 mb-3">
                            <ul class="mb-0 ps-3">
                                @foreach($errors->all() as $error)
                                    <li style="font-size:0.85rem;">{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="employee_no" class="form-label"><i class="bi bi-hash me-1 text-info"></i>Employee Number</label>
                        <input type="text" id="employee_no" name="employee_no"
                            class="form-control @error('employee_no') is-invalid @enderror"
                            placeholder="e.g. T001"
                            value="{{ old('employee_no') }}" maxlength="50">
                        @error('employee_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="full_name" class="form-label"><i class="bi bi-person me-1 text-info"></i>Full Name</label>
                        <input type="text" id="full_name" name="full_name"
                            class="form-control @error('full_name') is-invalid @enderror"
                            placeholder="e.g. Mr. Kasun Perera"
                            value="{{ old('full_name') }}" maxlength="255">
                        @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label"><i class="bi bi-envelope me-1 text-info"></i>Email Address</label>
                        <input type="email" id="email" name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            placeholder="e.g. teacher@school.lk"
                            value="{{ old('email') }}" maxlength="255">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label"><i class="bi bi-telephone me-1 text-info"></i>Phone Number</label>
                        <input type="text" id="phone" name="phone"
                            class="form-control @error('phone') is-invalid @enderror"
                            placeholder="e.g. 0771234567"
                            value="{{ old('phone') }}" maxlength="20">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-2">
                        <label for="specialization" class="form-label"><i class="bi bi-award me-1 text-info"></i>Specialization</label>
                        <input type="text" id="specialization" name="specialization"
                            class="form-control @error('specialization') is-invalid @enderror"
                            placeholder="e.g. Mathematics"
                            value="{{ old('specialization') }}" maxlength="255">
                        @error('specialization')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn fw-semibold text-white" id="submitBtn" style="background:#0ea5e9;">
                        <i class="bi bi-person-check me-1"></i>Add Teacher
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const form        = document.getElementById('teacherForm');
    const methodField = document.getElementById('methodField');
    const modalTitle  = document.getElementById('teacherModalLabel');
    const modalHeader = document.getElementById('modalHeader');
    const submitBtn   = document.getElementById('submitBtn');
    const storeUrl    = "{{ route('teachers.store') }}";

    document.querySelectorAll('.edit-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            form.action           = `/teachers/${id}`;
            methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            modalTitle.innerHTML  = '<i class="bi bi-pencil-square me-2"></i>Edit Teacher';
            modalHeader.style.background = '#f59e0b';
            submitBtn.style.background   = '#f59e0b';
            submitBtn.style.color        = '#000';
            submitBtn.innerHTML = '<i class="bi bi-save me-1"></i>Update Teacher';

            document.getElementById('employee_no').value    = this.dataset.employee_no;
            document.getElementById('full_name').value      = this.dataset.full_name;
            document.getElementById('email').value          = this.dataset.email;
            document.getElementById('phone').value          = this.dataset.phone;
            document.getElementById('specialization').value = this.dataset.specialization;
        });
    });

    document.getElementById('teacherModal').addEventListener('hidden.bs.modal', function() {
        form.action           = storeUrl;
        methodField.innerHTML = '';
        modalTitle.innerHTML  = '<i class="bi bi-person-plus-fill me-2"></i>Add New Teacher';
        modalHeader.style.background = '#0ea5e9';
        submitBtn.style.background   = '#0ea5e9';
        submitBtn.style.color        = '#fff';
        submitBtn.innerHTML   = '<i class="bi bi-person-check me-1"></i>Add Teacher';
        form.reset();
    });

    @if($errors->any())
        new bootstrap.Modal(document.getElementById('teacherModal')).show();
    @endif

    const searchInput = document.getElementById('searchInput');
    const clearBtn    = document.getElementById('clearSearch');
    const noResults   = document.getElementById('no-results');
    const countBadge  = document.getElementById('teacher-count');
    const rows        = document.querySelectorAll('#teacherTableBody tr');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            let visible = 0;
            rows.forEach(function(row) {
                const empNo    = row.querySelector('.col-emp-no')?.textContent.toLowerCase() || '';
                const fullName = row.querySelector('.col-full-name')?.textContent.toLowerCase() || '';
                const email    = row.querySelector('.col-email')?.textContent.toLowerCase() || '';
                if (empNo.includes(query) || fullName.includes(query) || email.includes(query)) {
                    row.style.display = ''; visible++;
                } else {
                    row.style.display = 'none';
                }
            });
            clearBtn.style.display  = query.length > 0 ? 'inline-block' : 'none';
            noResults.style.display = visible === 0 && query.length > 0 ? 'block' : 'none';
            countBadge.textContent  = visible + ' teacher' + (visible !== 1 ? 's' : '');
        });
        clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            searchInput.dispatchEvent(new Event('input'));
            searchInput.focus();
        });
    }
</script>
@endpush
