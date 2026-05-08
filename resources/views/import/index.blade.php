@extends('layouts.app')

@section('title', 'Import Data')
@section('page-title', 'Import Data')
@section('page-subtitle', 'Preview CSV or Excel data before saving it')

@section('content')

<div class="row justify-content-center">
    <div class="col-xl-10">
        <div class="card">
            <div class="card-header text-white py-3" style="background:linear-gradient(135deg,#0ea5e9,#38bdf8);">
                <i class="bi bi-box-arrow-in-down me-2"></i>Import Data
            </div>
            <div class="card-body p-4">

                @if($errors->any())
                    <div class="alert alert-danger rounded-3 mb-4">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $e)<li style="font-size:.85rem;">{{ $e }}</li>@endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('import.store') }}" method="POST" enctype="multipart/form-data" class="mb-4">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label fw-semibold mb-2">What are you importing?</label>
                        <div class="d-flex gap-2 flex-wrap">
                            @foreach([['students','people-fill','#6366f1','Students'],['teachers','person-workspace','#0ea5e9','Teachers'],['subjects','book-fill','#10b981','Subjects']] as [$val,$icon,$color,$label])
                            <label class="filter-chip" for="imp_{{ $val }}">
                                <input type="radio" name="data_type" id="imp_{{ $val }}" value="{{ $val }}" class="d-none"
                                    {{ old('data_type', $preview['type'] ?? 'students') == $val ? 'checked' : '' }}>
                                <i class="bi bi-{{ $icon }}" style="color:{{ $color }};"></i>
                                {{ $label }}
                            </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold mb-2"><i class="bi bi-upload me-1 text-info"></i>Upload File</label>
                        <div id="dropZone" class="rounded-3 p-5 text-center"
                             style="border:2px dashed #cbd5e1; cursor:pointer; transition:.2s;">
                            <i class="bi bi-cloud-upload" style="font-size:2.2rem;color:#94a3b8;"></i>
                            <p class="mt-2 mb-1 fw-semibold text-secondary" style="font-size:.9rem;">Click to browse or drag and drop file here</p>
                            <p class="text-muted mb-0" style="font-size:.78rem;">CSV, XLSX or XLS, max 10 MB</p>
                            <input type="file" name="file" id="fileInput" accept=".csv,.xlsx,.xls"
                                   class="d-none @error('file') is-invalid @enderror">
                        </div>
                        <div id="fileInfo" class="mt-2 text-success small" style="display:none;">
                            <i class="bi bi-check-circle me-1"></i><span id="fileName"></span>
                        </div>
                        @error('file')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-info fw-semibold text-white">
                            <i class="bi bi-eye me-2"></i>Preview Import
                        </button>
                    </div>
                </form>

                @if($preview)
                <div class="import-preview border rounded-3 overflow-hidden">
                    <div class="d-flex flex-wrap gap-2 justify-content-between align-items-center px-3 py-3 border-bottom" style="background:#f8faff;">
                        <div>
                            <h6 class="mb-1 fw-semibold">{{ $preview['label'] }} Preview</h6>
                            <div class="text-muted small">
                                {{ $preview['file_name'] }} · {{ $preview['total_rows'] }} row(s) found ·
                                {{ $preview['valid_rows'] }} ready · {{ $preview['skipped_rows'] }} need attention
                            </div>
                        </div>
                        <form action="{{ route('import.confirm') }}" method="POST">
                            @csrf
                            <input type="hidden" name="token" value="{{ $preview['token'] }}">
                            <button type="submit" class="btn btn-dark fw-semibold" {{ $preview['valid_rows'] === 0 ? 'disabled' : '' }}>
                                <i class="bi bi-database-check me-1"></i>Import {{ $preview['valid_rows'] }} Row{{ $preview['valid_rows'] === 1 ? '' : 's' }}
                            </button>
                        </form>
                    </div>

                    <div class="px-3 py-2 border-bottom bg-white">
                        <div class="small fw-semibold mb-2">Detected column mapping</div>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($preview['columns'] as $column)
                                <span class="mapping-pill">
                                    <strong>{{ str_replace('_', ' ', $column) }}</strong>
                                    <i class="bi bi-arrow-left-right mx-1"></i>
                                    {{ $preview['mapping'][$column]['header'] ?? 'not found' }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <div class="table-responsive" style="max-height:360px;">
                        <table class="table table-bordered table-hover align-middle mb-0" style="font-size:.82rem;">
                            <thead style="position:sticky;top:0;z-index:1;">
                                <tr>
                                    <th style="background:#111827;color:#fff;">Status</th>
                                    @foreach($preview['columns'] as $column)
                                        <th style="background:#111827;color:#fff;">{{ ucwords(str_replace('_', ' ', $column)) }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($preview['rows'] as $row)
                                <tr>
                                    <td>
                                        @if($row['_valid'])
                                            <span class="badge rounded-pill" style="background:#d1fae5;color:#065f46;">Ready</span>
                                        @else
                                            <span class="badge rounded-pill" style="background:#fee2e2;color:#991b1b;">Skipped</span>
                                            <div class="text-danger small mt-1">{{ implode(', ', $row['_errors']) }}</div>
                                        @endif
                                    </td>
                                    @foreach($preview['columns'] as $column)
                                        <td>{{ $row[$column] !== '' ? $row[$column] : '—' }}</td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<style>
    .filter-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 14px;
        border: 1.5px solid #e2e8f0;
        border-radius: 999px;
        font-size: .85rem;
        font-weight: 500;
        cursor: pointer;
        background: #fff;
        color: #374151;
        transition: all .15s;
        user-select: none;
    }
    .filter-chip:hover { border-color:#0ea5e9; background:#f0f9ff; }
    .filter-chip:has(input:checked) {
        border-color: #0ea5e9;
        background: #e0f2fe;
        color: #0369a1;
        box-shadow: 0 0 0 2px #bae6fd;
    }
    #dropZone:hover { border-color:#6366f1 !important; background:#f8faff; }
    #dropZone.dragover { border-color:#6366f1 !important; background:#eef2ff; }
    .mapping-pill {
        display:inline-flex;
        align-items:center;
        gap:2px;
        padding:4px 8px;
        border-radius:999px;
        background:#eef2ff;
        color:#3730a3;
        font-size:.76rem;
    }
</style>
<script>
    const dropZone  = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const fileInfo  = document.getElementById('fileInfo');
    const fileName  = document.getElementById('fileName');

    dropZone.addEventListener('click', () => fileInput.click());

    fileInput.addEventListener('change', function() {
        if (this.files[0]) {
            fileName.textContent = this.files[0].name;
            fileInfo.style.display = 'block';
        }
    });

    dropZone.addEventListener('dragover', e => { e.preventDefault(); dropZone.classList.add('dragover'); });
    dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        fileInput.files = e.dataTransfer.files;
        if (fileInput.files[0]) {
            fileName.textContent = fileInput.files[0].name;
            fileInfo.style.display = 'block';
        }
    });
</script>
@endpush
