@extends('layouts.app')

@section('title', 'Students')
@section('page-title', 'Students')
@section('page-subtitle', 'Manage student registrations')

@section('content')

<div class="card">

    {{-- ── Table Header: Search + Add Button ── --}}
    <div class="card-header bg-dark text-white py-3 d-flex flex-wrap gap-2 justify-content-between align-items-center">
        <span><i class="bi bi-people-fill me-2"></i>All Registered Students
            <span class="badge bg-primary rounded-pill ms-2" id="student-count">
                {{ $students->count() }} student{{ $students->count() !== 1 ? 's' : '' }}
            </span>
        </span>
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <div class="input-group" style="width:280px;">
                <span class="input-group-text bg-white border-end-0 py-1"><i class="bi bi-search text-muted small"></i></span>
                <input type="text" id="searchInput" class="form-control border-start-0 ps-0 py-1"
                    placeholder="Search Reg No, Name, Email..." autocomplete="off" style="font-size:0.85rem;">
                <button class="btn btn-outline-secondary btn-sm" type="button" id="clearSearch" style="display:none;">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <button type="button" class="btn btn-primary btn-sm fw-semibold px-3" data-bs-toggle="modal" data-bs-target="#studentModal">
                <i class="bi bi-person-plus-fill me-1"></i>Register New Student
            </button>
        </div>
    </div>

    {{-- ── Table ── --}}
    <div class="card-body p-0">
        <div id="no-results" class="text-center text-muted py-3" style="display:none;">
            <i class="bi bi-search me-1"></i>No students found matching your search.
        </div>
        @if($students->isEmpty())
            <div class="empty-state">
                <i class="bi bi-inbox"></i>
                No students registered yet. Click "Register New Student" to add one!
            </div>
        @else
            <div class="table-responsive" style="max-height: calc(100vh - 210px); overflow-y: auto;">
                <table class="table table-hover table-bordered mb-0 align-middle">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:48px;">#</th>
                            <th>Reg No</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Date of Birth</th>
                            <th class="text-center" style="width:110px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="studentTableBody">
                        @foreach($students as $index => $s)
                            <tr>
                                <td class="text-center text-muted small">{{ $index + 1 }}</td>
                                <td><span class="badge-code col-reg-no">{{ $s->reg_no }}</span></td>
                                <td class="fw-semibold col-full-name">{{ $s->full_name }}</td>
                                <td class="text-muted small col-email">{{ $s->email }}</td>
                                <td>{{ $s->phone }}</td>
                                <td>{{ \Carbon\Carbon::parse($s->dob)->format('d M Y') }}</td>
                                <td class="text-center">
                                    {{-- Edit: open modal pre-filled via JS --}}
                                    <button type="button"
                                        class="btn btn-sm btn-outline-warning btn-action me-1 edit-btn"
                                        title="Edit"
                                        data-id="{{ $s->id }}"
                                        data-reg_no="{{ $s->reg_no }}"
                                        data-full_name="{{ $s->full_name }}"
                                        data-email="{{ $s->email }}"
                                        data-phone="{{ $s->phone }}"
                                        data-dob="{{ $s->dob }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#studentModal">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    {{-- Delete --}}
                                    <button type="button"
                                        class="btn btn-sm btn-outline-danger btn-action delete-trigger"
                                        title="Delete"
                                        data-name="{{ $s->full_name }}"
                                        data-form="delete-student-{{ $s->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <form id="delete-student-{{ $s->id }}"
                                          action="{{ route('students.destroy', $s->id) }}"
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

{{-- ══════════ MODAL ══════════ --}}
<div class="modal fade" id="studentModal" tabindex="-1" aria-labelledby="studentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header text-white" id="modalHeader" style="background:#6366f1;">
                <h5 class="modal-title" id="studentModalLabel">
                    <i class="bi bi-person-plus-fill me-2"></i>Register New Student
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="studentForm" method="POST" novalidate>
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
                        <label for="reg_no" class="form-label"><i class="bi bi-hash me-1 text-primary"></i>Registration Number</label>
                        <input type="text" id="reg_no" name="reg_no"
                            class="form-control @error('reg_no') is-invalid @enderror"
                            placeholder="e.g. REG2024001"
                            value="{{ old('reg_no') }}" maxlength="50">
                        @error('reg_no')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="full_name" class="form-label"><i class="bi bi-person me-1 text-primary"></i>Full Name</label>
                        <input type="text" id="full_name" name="full_name"
                            class="form-control @error('full_name') is-invalid @enderror"
                            placeholder="e.g. Dinith Tharindu"
                            value="{{ old('full_name') }}" maxlength="255">
                        @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label"><i class="bi bi-envelope me-1 text-primary"></i>Email Address</label>
                        <input type="email" id="email" name="email"
                            class="form-control @error('email') is-invalid @enderror"
                            placeholder="e.g. dinithmain@gmail.com"
                            value="{{ old('email') }}" maxlength="255">
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label"><i class="bi bi-telephone me-1 text-primary"></i>Phone Number</label>
                        <input type="text" id="phone" name="phone"
                            class="form-control @error('phone') is-invalid @enderror"
                            placeholder="e.g. 0771234567"
                            value="{{ old('phone') }}" maxlength="20">
                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-2">
                        <label for="dob" class="form-label"><i class="bi bi-calendar3 me-1 text-primary"></i>Date of Birth</label>
                        <input type="date" id="dob" name="dob"
                            class="form-control @error('dob') is-invalid @enderror"
                            value="{{ old('dob') }}"
                            max="{{ date('Y-m-d', strtotime('-1 day')) }}">
                        @error('dob')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary fw-semibold" id="submitBtn">
                        <i class="bi bi-person-check me-1"></i>Register Student
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const form        = document.getElementById('studentForm');
    const methodField = document.getElementById('methodField');
    const modalTitle  = document.getElementById('studentModalLabel');
    const modalHeader = document.getElementById('modalHeader');
    const submitBtn   = document.getElementById('submitBtn');
    const storeUrl    = "{{ route('students.store') }}";

    // Edit button clicked — pre-fill the modal
    document.querySelectorAll('.edit-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            form.action    = `/students/${id}`;
            methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            modalTitle.innerHTML  = '<i class="bi bi-pencil-square me-2"></i>Edit Student';
            modalHeader.style.background = '#f59e0b';
            submitBtn.className = 'btn btn-warning fw-semibold';
            submitBtn.innerHTML = '<i class="bi bi-save me-1"></i>Update Student';

            document.getElementById('reg_no').value    = this.dataset.reg_no;
            document.getElementById('full_name').value = this.dataset.full_name;
            document.getElementById('email').value     = this.dataset.email;
            document.getElementById('phone').value     = this.dataset.phone;
            document.getElementById('dob').value       = this.dataset.dob;
        });
    });

    // When modal is hidden, reset to Add mode
    document.getElementById('studentModal').addEventListener('hidden.bs.modal', function() {
        form.action           = storeUrl;
        methodField.innerHTML = '';
        modalTitle.innerHTML  = '<i class="bi bi-person-plus-fill me-2"></i>Register New Student';
        modalHeader.style.background = '#6366f1';
        submitBtn.className   = 'btn btn-primary fw-semibold';
        submitBtn.innerHTML   = '<i class="bi bi-person-check me-1"></i>Register Student';
        form.reset();
    });

    // If there are validation errors, re-open the modal automatically
    @if($errors->any())
        var modal = new bootstrap.Modal(document.getElementById('studentModal'));
        modal.show();
    @endif

    // Live search
    const searchInput = document.getElementById('searchInput');
    const clearBtn    = document.getElementById('clearSearch');
    const noResults   = document.getElementById('no-results');
    const countBadge  = document.getElementById('student-count');
    const rows        = document.querySelectorAll('#studentTableBody tr');

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            let visible = 0;
            rows.forEach(function(row) {
                const regNo    = row.querySelector('.col-reg-no')?.textContent.toLowerCase() || '';
                const fullName = row.querySelector('.col-full-name')?.textContent.toLowerCase() || '';
                const email    = row.querySelector('.col-email')?.textContent.toLowerCase() || '';
                if (regNo.includes(query) || fullName.includes(query) || email.includes(query)) {
                    row.style.display = ''; visible++;
                } else {
                    row.style.display = 'none';
                }
            });
            clearBtn.style.display  = query.length > 0 ? 'inline-block' : 'none';
            noResults.style.display = visible === 0 && query.length > 0 ? 'block' : 'none';
            countBadge.textContent  = visible + ' student' + (visible !== 1 ? 's' : '');
        });
        clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            searchInput.dispatchEvent(new Event('input'));
            searchInput.focus();
        });
    }
</script>
@endpush
