@extends('layouts.app')

@section('title', 'Subjects')
@section('page-title', 'Subjects')
@section('page-subtitle', 'Manage school subjects')

@section('content')

<div class="card">

    <div class="card-header text-white py-3 d-flex flex-wrap gap-2 justify-content-between align-items-center"
         style="background: linear-gradient(135deg,#10b981,#34d399);">
        <span><i class="bi bi-book-fill me-2"></i>All Subjects
            <span class="badge bg-white text-success rounded-pill ms-2" id="subject-count">
                {{ $subjects->count() }} subject{{ $subjects->count() !== 1 ? 's' : '' }}
            </span>
        </span>
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <div class="input-group" style="width:280px;">
                <span class="input-group-text bg-white border-end-0 py-1"><i class="bi bi-search text-muted small"></i></span>
                <input type="text" id="searchInput" class="form-control border-start-0 ps-0 py-1"
                    placeholder="Search Code or Subject Name..." autocomplete="off" style="font-size:0.85rem;">
                <button class="btn btn-outline-secondary btn-sm" type="button" id="clearSearch" style="display:none;">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <button type="button" class="btn btn-light btn-sm fw-semibold px-3 text-success" data-bs-toggle="modal" data-bs-target="#subjectModal">
                <i class="bi bi-plus-circle me-1"></i>Add New Subject
            </button>
        </div>
    </div>

    <div class="card-body p-0">
        <div id="no-results" class="text-center text-muted py-3" style="display:none;">
            <i class="bi bi-search me-1"></i>No subjects found.
        </div>
        @if($subjects->isEmpty())
            <div class="empty-state">
                <i class="bi bi-book"></i>
                No subjects added yet. Click "Add New Subject" to add one!
            </div>
        @else
            <div class="table-responsive" style="max-height: calc(100vh - 210px); overflow-y: auto;">
                <table class="table table-hover table-bordered mb-0 align-middle">
                    <thead>
                        <tr>
                            <th class="text-center" style="width:48px;">#</th>
                            <th>Code</th>
                            <th>Subject Name</th>
                            <th>Description</th>
                            <th class="text-center" style="width:110px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="subjectTableBody">
                        @foreach($subjects as $index => $sub)
                            <tr>
                                <td class="text-center text-muted small">{{ $index + 1 }}</td>
                                <td><span class="badge-code col-code" style="background:#d1fae5;color:#065f46;">{{ $sub->subject_code }}</span></td>
                                <td class="fw-semibold col-name">{{ $sub->subject_name }}</td>
                                <td class="text-muted small">{{ $sub->description ?? '—' }}</td>
                                <td class="text-center">
                                    <button type="button"
                                        class="btn btn-sm btn-outline-warning btn-action me-1 edit-btn"
                                        title="Edit"
                                        data-id="{{ $sub->id }}"
                                        data-subject_code="{{ $sub->subject_code }}"
                                        data-subject_name="{{ $sub->subject_name }}"
                                        data-description="{{ $sub->description }}"
                                        data-bs-toggle="modal"
                                        data-bs-target="#subjectModal">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button type="button"
                                        class="btn btn-sm btn-outline-danger btn-action delete-trigger"
                                        title="Delete"
                                        data-name="{{ $sub->subject_name }}"
                                        data-form="delete-subject-{{ $sub->id }}">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <form id="delete-subject-{{ $sub->id }}"
                                          action="{{ route('subjects.destroy', $sub->id) }}"
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
<div class="modal fade" id="subjectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">

            <div class="modal-header text-white" id="modalHeader" style="background:#10b981;">
                <h5 class="modal-title" id="subjectModalLabel">
                    <i class="bi bi-plus-circle me-2"></i>Add New Subject
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="subjectForm" method="POST" novalidate>
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
                        <label for="subject_code" class="form-label"><i class="bi bi-hash me-1 text-success"></i>Subject Code</label>
                        <input type="text" id="subject_code" name="subject_code"
                            class="form-control @error('subject_code') is-invalid @enderror"
                            placeholder="e.g. CS101"
                            value="{{ old('subject_code') }}" maxlength="50">
                        @error('subject_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label for="subject_name" class="form-label"><i class="bi bi-book me-1 text-success"></i>Subject Name</label>
                        <input type="text" id="subject_name" name="subject_name"
                            class="form-control @error('subject_name') is-invalid @enderror"
                            placeholder="e.g. Computer Science"
                            value="{{ old('subject_name') }}" maxlength="255">
                        @error('subject_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-2">
                        <label for="description" class="form-label"><i class="bi bi-card-text me-1 text-success"></i>Description <span class="text-muted">(optional)</span></label>
                        <textarea id="description" name="description" rows="3"
                            class="form-control @error('description') is-invalid @enderror"
                            placeholder="Brief description..." maxlength="500">{{ old('description') }}</textarea>
                        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                </div>

                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn fw-semibold text-white" id="submitBtn" style="background:#10b981;">
                        <i class="bi bi-plus-circle me-1"></i>Add Subject
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const form        = document.getElementById('subjectForm');
    const methodField = document.getElementById('methodField');
    const modalTitle  = document.getElementById('subjectModalLabel');
    const modalHeader = document.getElementById('modalHeader');
    const submitBtn   = document.getElementById('submitBtn');
    const storeUrl    = "{{ route('subjects.store') }}";

    document.querySelectorAll('.edit-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const id = this.dataset.id;
            form.action    = `/subjects/${id}`;
            methodField.innerHTML = '<input type="hidden" name="_method" value="PUT">';
            modalTitle.innerHTML  = '<i class="bi bi-pencil-square me-2"></i>Edit Subject';
            modalHeader.style.background = '#f59e0b';
            submitBtn.style.background   = '#f59e0b';
            submitBtn.style.color        = '#000';
            submitBtn.innerHTML = '<i class="bi bi-save me-1"></i>Update Subject';

            document.getElementById('subject_code').value = this.dataset.subject_code;
            document.getElementById('subject_name').value = this.dataset.subject_name;
            document.getElementById('description').value  = this.dataset.description || '';
        });
    });

    document.getElementById('subjectModal').addEventListener('hidden.bs.modal', function() {
        form.action           = storeUrl;
        methodField.innerHTML = '';
        modalTitle.innerHTML  = '<i class="bi bi-plus-circle me-2"></i>Add New Subject';
        modalHeader.style.background = '#10b981';
        submitBtn.style.background   = '#10b981';
        submitBtn.style.color        = '#fff';
        submitBtn.innerHTML   = '<i class="bi bi-plus-circle me-1"></i>Add Subject';
        form.reset();
    });

    @if($errors->any())
        var modal = new bootstrap.Modal(document.getElementById('subjectModal'));
        modal.show();
    @endif

    const searchInput = document.getElementById('searchInput');
    const clearBtn    = document.getElementById('clearSearch');
    const noResults   = document.getElementById('no-results');
    const countBadge  = document.getElementById('subject-count');
    const rows        = document.querySelectorAll('#subjectTableBody tr');

    if (searchInput) {
        searchInput.addEventListener('input', function () {
            const query = this.value.toLowerCase().trim();
            let visible = 0;
            rows.forEach(function(row) {
                const code = row.querySelector('.col-code')?.textContent.toLowerCase() || '';
                const name = row.querySelector('.col-name')?.textContent.toLowerCase() || '';
                if (code.includes(query) || name.includes(query)) {
                    row.style.display = ''; visible++;
                } else {
                    row.style.display = 'none';
                }
            });
            clearBtn.style.display  = query.length > 0 ? 'inline-block' : 'none';
            noResults.style.display = visible === 0 && query.length > 0 ? 'block' : 'none';
            countBadge.textContent  = visible + ' subject' + (visible !== 1 ? 's' : '');
        });
        clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            searchInput.dispatchEvent(new Event('input'));
            searchInput.focus();
        });
    }
</script>
@endpush
