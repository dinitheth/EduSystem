@extends('layouts.app')
@section('title','Pending Students')
@section('page-title','Pending Students')
@section('page-subtitle','Review new website registration requests')

@section('content')
<div class="card">
    <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
        <span><i class="bi bi-person-lines-fill me-2"></i>Pending Student Requests</span>
        <span class="badge bg-warning text-dark rounded-pill">{{ $pendingStudents->where('status', 'Pending')->count() }} pending</span>
    </div>
    <div class="card-body p-0">
        @if($pendingStudents->isEmpty())
            <div class="empty-state"><i class="bi bi-inbox"></i>No pending student requests yet.</div>
        @else
            <div class="table-responsive">
                <table class="table table-hover table-bordered mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>DOB</th>
                            <th>Gender</th>
                            <th>Requested Subjects</th>
                            <th>Status</th>
                            <th class="text-center" style="width: 180px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingStudents as $pendingStudent)
                            @php
                                $requestedIds = $pendingStudent->requested_subject_ids ?? [];
                                $requestedSubjects = $subjects->whereIn('id', $requestedIds);
                                $requestedSubjectPayload = $requestedSubjects->map(function ($subject) {
                                    return [
                                        'id' => $subject->id,
                                        'subject_name' => $subject->subject_name,
                                        'subject_code' => $subject->subject_code,
                                    ];
                                })->values()->toJson();
                            @endphp
                            <tr>
                                <td class="fw-semibold">{{ $pendingStudent->full_name }}</td>
                                <td>{{ $pendingStudent->email }}</td>
                                <td>{{ $pendingStudent->phone }}</td>
                                <td>{{ optional($pendingStudent->dob)->format('d M Y') }}</td>
                                <td>{{ $pendingStudent->gender }}</td>
                                <td>
                                    @forelse($requestedSubjects as $subject)
                                        <span class="badge rounded-pill me-1" style="background:#eef2ff;color:#4338ca;">{{ $subject->subject_name }}</span>
                                    @empty
                                        <span class="text-muted">No subjects selected</span>
                                    @endforelse
                                </td>
                                <td>
                                    <span class="badge rounded-pill" style="background:{{ $pendingStudent->status === 'Pending' ? '#fef3c7' : ($pendingStudent->status === 'Approved' ? '#dcfce7' : '#fee2e2') }};color:{{ $pendingStudent->status === 'Pending' ? '#92400e' : ($pendingStudent->status === 'Approved' ? '#166534' : '#991b1b') }};">
                                        {{ $pendingStudent->status }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($pendingStudent->status === 'Pending')
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-success me-1 approve-btn"
                                            data-id="{{ $pendingStudent->id }}"
                                            data-name="{{ $pendingStudent->full_name }}"
                                            data-subjects='{{ $requestedSubjectPayload }}'
                                            data-bs-toggle="modal"
                                            data-bs-target="#approveModal"
                                        >
                                            <i class="bi bi-check2-circle"></i>
                                        </button>
                                        <form action="{{ route('pending-students.dismiss', $pendingStudent) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-x-circle"></i>
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-muted small">No actions</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>

<div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content border-0 shadow-lg">
      <div class="modal-header text-white" style="background:#0f766e;">
        <h5 class="modal-title"><i class="bi bi-person-check me-2"></i>Approve Pending Student</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>
      <form id="approveForm" method="POST">
        @csrf
        <div class="modal-body p-4">
          <div class="mb-3">
            <div class="fw-semibold text-dark" id="approveStudentName">Student</div>
            <div class="text-muted small">Approval creates the student account, assigns a class, links subjects, and generates a registration number automatically.</div>
          </div>
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">Assign Class</label>
              <select name="class" class="form-select" required>
                <option value="">Select class...</option>
                <option value="A">Class A</option>
                <option value="B">Class B</option>
                <option value="C">Class C</option>
                <option value="D">Class D</option>
              </select>
            </div>
            <div class="col-md-8">
              <label class="form-label">Final Subject Assignment</label>
              <div class="border rounded-3 p-3" style="background:#f8fafc;">
                <div id="approveSubjectList" class="row g-2"></div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success fw-semibold">Approve Student</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
const approveForm = document.getElementById('approveForm');
const approveStudentName = document.getElementById('approveStudentName');
const approveSubjectList = document.getElementById('approveSubjectList');

document.querySelectorAll('.approve-btn').forEach((button) => {
    button.addEventListener('click', () => {
        approveForm.action = `/pending-students/${button.dataset.id}/approve`;
        approveStudentName.textContent = button.dataset.name;

        const requestedSubjects = JSON.parse(button.dataset.subjects || '[]');
        approveSubjectList.innerHTML = requestedSubjects.length
            ? requestedSubjects.map((subject) => `
            <div class="col-md-6">
                <label class="d-flex align-items-start gap-2 border rounded-3 p-2 bg-white">
                    <input type="checkbox" name="subject_ids[]" value="${subject.id}" class="form-check-input mt-1" checked>
                    <span>
                        <span class="fw-semibold d-block">${subject.subject_name}</span>
                        <span class="text-muted small">${subject.subject_code}</span>
                    </span>
                </label>
            </div>
        `).join('')
            : '<div class="col-12 text-muted small">No subjects were selected in the website registration.</div>';
    });
});
</script>
@endpush
