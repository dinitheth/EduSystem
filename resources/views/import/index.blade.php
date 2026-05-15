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
                @if(session('success'))
                    <div class="alert alert-success rounded-3 mb-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger rounded-3 mb-4">
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $e)
                                <li style="font-size:.85rem;">{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div id="importProgressPanel" class="alert alert-info rounded-3 mb-4 d-none" role="status" aria-live="polite">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <strong id="importProgressTitle">Uploading file...</strong>
                        <span id="importProgressPercent">0%</span>
                    </div>
                    <div class="progress" style="height: 10px;">
                        <div
                            id="importProgressBar"
                            class="progress-bar progress-bar-striped progress-bar-animated"
                            role="progressbar"
                            style="width: 0%;"
                            aria-valuemin="0"
                            aria-valuemax="100"
                            aria-valuenow="0"
                        ></div>
                    </div>
                    <div id="importProgressNote" class="small mt-2 text-secondary">Preparing upload...</div>
                </div>

                <div id="importAjaxError" class="alert alert-danger rounded-3 mb-4 d-none"></div>

                <form id="previewImportForm" action="{{ route('import.store') }}" method="POST" enctype="multipart/form-data" class="mb-4">
                    @csrf

                    <div class="mb-4">
                        <label class="form-label fw-semibold mb-2">What are you importing?</label>
                        <div class="d-flex gap-2 flex-wrap">
                            @foreach([['students','people-fill','#6366f1','Students'],['teachers','person-workspace','#0ea5e9','Teachers'],['subjects','book-fill','#10b981','Subjects']] as [$val,$icon,$color,$label])
                                <label class="filter-chip" for="imp_{{ $val }}">
                                    <input
                                        type="radio"
                                        name="data_type"
                                        id="imp_{{ $val }}"
                                        value="{{ $val }}"
                                        class="d-none"
                                        {{ old('data_type', $preview['type'] ?? 'students') == $val ? 'checked' : '' }}
                                    >
                                    <i class="bi bi-{{ $icon }}" style="color:{{ $color }};"></i>
                                    {{ $label }}
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-semibold mb-2"><i class="bi bi-upload me-1 text-info"></i>Upload File</label>
                        <div
                            id="dropZone"
                            class="rounded-3 p-5 text-center"
                            style="border:2px dashed #cbd5e1; cursor:pointer; transition:.2s;"
                        >
                            <i class="bi bi-cloud-upload" style="font-size:2.2rem;color:#94a3b8;"></i>
                            <p class="mt-2 mb-1 fw-semibold text-secondary" style="font-size:.9rem;">Click to browse or drag and drop file here</p>
                            <p class="text-muted mb-0" style="font-size:.78rem;">CSV, XLSX or XLS, max 10 MB</p>
                            <input
                                type="file"
                                name="file"
                                id="fileInput"
                                accept=".csv,.xlsx,.xls"
                                class="d-none @error('file') is-invalid @enderror"
                            >
                        </div>
                        <div id="fileInfo" class="mt-2 text-success small" style="{{ $preview ? 'display:block;' : 'display:none;' }}">
                            <i class="bi bi-check-circle me-1"></i><span id="fileName">{{ $preview['file_name'] ?? '' }}</span>
                        </div>
                        @error('file')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <button type="submit" class="btn btn-info fw-semibold text-white flex-grow-1">
                            <i class="bi bi-eye me-2"></i>Preview Import
                        </button>
                    </div>
                </form>

                @if($preview)
                    <div class="import-preview border rounded-3 overflow-hidden">
                        <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center px-3 py-3 border-bottom" style="background:#f8faff;">
                            <div>
                                <h6 class="mb-1 fw-semibold">{{ $preview['label'] }} Preview</h6>
                                <div class="text-muted small mb-2">
                                    {{ $preview['file_name'] }}
                                </div>
                                <div class="d-flex flex-wrap gap-2 small">
                                    <span class="summary-pill">
                                        <strong>{{ $preview['total_rows'] }}</strong> rows
                                    </span>
                                    <span class="summary-pill summary-pill-success">
                                        <strong>{{ $preview['valid_rows'] }}</strong> ready
                                    </span>
                                    <span class="summary-pill summary-pill-warning">
                                        <strong>{{ $preview['skipped_rows'] }}</strong> skipped
                                    </span>
                                    <span class="summary-pill">
                                        Preview {{ $preview['batch_start'] }}-{{ $preview['batch_end'] }}
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex flex-wrap gap-2">
                                <form id="clearImportForm" action="{{ route('import.clear') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="token" value="{{ $preview['token'] }}">
                                    <button type="submit" class="btn btn-outline-secondary fw-semibold">
                                        <i class="bi bi-trash3 me-1"></i>Clear Import
                                    </button>
                                </form>
                                <form id="confirmImportForm" action="{{ route('import.confirm') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="token" value="{{ $preview['token'] }}">
                                    <button type="submit" class="btn btn-dark fw-semibold" {{ $preview['valid_rows'] === 0 ? 'disabled' : '' }}>
                                        <i class="bi bi-database-check me-1"></i>Import All Valid Rows
                                    </button>
                                </form>
                            </div>
                        </div>

                        <div id="inlineImportProgressPanel" class="px-3 py-3 border-bottom bg-white d-none" role="status" aria-live="polite">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong id="inlineImportProgressTitle">Import progress</strong>
                                <span id="inlineImportProgressPercent">0%</span>
                            </div>
                            <div class="progress" style="height: 10px;">
                                <div
                                    id="inlineImportProgressBar"
                                    class="progress-bar progress-bar-striped progress-bar-animated"
                                    role="progressbar"
                                    style="width: 0%;"
                                    aria-valuemin="0"
                                    aria-valuemax="100"
                                    aria-valuenow="0"
                                ></div>
                            </div>
                            <div id="inlineImportProgressNote" class="small mt-2 text-secondary">Waiting to start import...</div>
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
                                                <td>{{ $row[$column] !== '' ? $row[$column] : '-' }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if($preview['has_more_batches'])
                            <div class="px-3 py-3 border-top bg-light small text-muted">
                                This preview shows the first {{ $preview['batch_size'] }} rows. When you start import, the backend will process the full file automatically in 50-row batches.
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1085;">
    <div id="importStatusToast" class="toast border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header border-0">
            <i id="importToastIcon" class="bi bi-info-circle-fill me-2"></i>
            <strong id="importToastTitle" class="me-auto">Notification</strong>
            <small id="importToastTime">Now</small>
            <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
        </div>
        <div id="importToastMessage" class="toast-body"></div>
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
    .summary-pill {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 5px 10px;
        border-radius: 999px;
        background: #f3f4f6;
        color: #374151;
        font-size: .78rem;
        font-weight: 500;
    }
    .summary-pill-success {
        background: #dcfce7;
        color: #166534;
    }
    .summary-pill-warning {
        background: #fee2e2;
        color: #991b1b;
    }
    #importStatusToast.toast-success .toast-header,
    #importStatusToast.toast-success .toast-body {
        background: #ecfdf5;
        color: #065f46;
    }
    #importStatusToast.toast-danger .toast-header,
    #importStatusToast.toast-danger .toast-body {
        background: #fef2f2;
        color: #991b1b;
    }
    #importStatusToast.toast-info .toast-header,
    #importStatusToast.toast-info .toast-body {
        background: #eff6ff;
        color: #1d4ed8;
    }
</style>
<script>
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const previewImportForm = document.getElementById('previewImportForm');
    const confirmImportForm = document.getElementById('confirmImportForm');
    const clearImportForm = document.getElementById('clearImportForm');
    const progressPanel = document.getElementById('importProgressPanel');
    const progressBar = document.getElementById('importProgressBar');
    const progressPercent = document.getElementById('importProgressPercent');
    const progressTitle = document.getElementById('importProgressTitle');
    const progressNote = document.getElementById('importProgressNote');
    const inlineProgressPanel = document.getElementById('inlineImportProgressPanel');
    const inlineProgressBar = document.getElementById('inlineImportProgressBar');
    const inlineProgressPercent = document.getElementById('inlineImportProgressPercent');
    const inlineProgressTitle = document.getElementById('inlineImportProgressTitle');
    const inlineProgressNote = document.getElementById('inlineImportProgressNote');
    const ajaxError = document.getElementById('importAjaxError');
    const importToastEl = document.getElementById('importStatusToast');
    const importToastTitle = document.getElementById('importToastTitle');
    const importToastMessage = document.getElementById('importToastMessage');
    const importToastIcon = document.getElementById('importToastIcon');
    let importToast = null;

    function showImportToast(type, title, message) {
        if (!importToastEl || !window.bootstrap) {
            return;
        }

        importToastEl.classList.remove('toast-success', 'toast-danger', 'toast-info');
        importToastEl.classList.add(`toast-${type}`);
        importToastTitle.textContent = title;
        importToastMessage.textContent = message;

        const iconMap = {
            success: 'bi-check-circle-fill',
            danger: 'bi-exclamation-triangle-fill',
            info: 'bi-info-circle-fill'
        };

        importToastIcon.className = `bi ${iconMap[type] || iconMap.info} me-2`;

        if (!importToast) {
            importToast = new bootstrap.Toast(importToastEl, { delay: 4500 });
        }

        importToast.show();
    }

    function setProgressState({ title, percent, note, style = 'info', indeterminate = false }) {
        const applyState = (panel, bar, label, titleNode, noteNode, panelClass) => {
            if (!panel || !bar || !label || !titleNode || !noteNode) {
                return;
            }

            panel.className = panelClass;
            panel.classList.remove('d-none');
            titleNode.textContent = title;
            label.textContent = indeterminate ? '' : `${percent}%`;
            noteNode.textContent = note;
            bar.style.width = indeterminate ? '100%' : `${percent}%`;
            bar.setAttribute('aria-valuenow', indeterminate ? '100' : String(percent));
            bar.classList.toggle('progress-bar-animated', true);
            bar.classList.toggle('progress-bar-striped', true);
        };

        applyState(progressPanel, progressBar, progressPercent, progressTitle, progressNote, `alert alert-${style} rounded-3 mb-4`);
        applyState(inlineProgressPanel, inlineProgressBar, inlineProgressPercent, inlineProgressTitle, inlineProgressNote, 'px-3 py-3 border-bottom bg-white');
    }

    function hideProgressState() {
        if (progressPanel) {
            progressPanel.classList.add('d-none');
        }

        if (inlineProgressPanel) {
            inlineProgressPanel.classList.add('d-none');
        }
    }

    function showAjaxError(message) {
        ajaxError.textContent = message;
        ajaxError.classList.remove('d-none');
        showImportToast('danger', 'Import failed', message);
    }

    function hideAjaxError() {
        ajaxError.classList.add('d-none');
        ajaxError.textContent = '';
    }

    function firstErrorMessage(payload, fallback) {
        if (payload && payload.errors) {
            const firstKey = Object.keys(payload.errors)[0];
            const value = payload.errors[firstKey];

            if (Array.isArray(value) && value.length > 0) {
                return value[0];
            }

            if (typeof value === 'string') {
                return value;
            }
        }

        return payload?.message || fallback;
    }

    function redirectToResponse(payload) {
        if (payload?.redirect) {
            window.location.href = payload.redirect;
            return;
        }

        window.location.reload();
    }

    function submitWithAjax(form, options) {
        if (!form) return;

        hideAjaxError();
        const xhr = new XMLHttpRequest();
        const formData = new FormData(form);

        xhr.open(form.method || 'POST', form.action, true);
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        if (options.uploadProgress) {
            setProgressState({
                title: 'Uploading file...',
                percent: 0,
                note: 'Starting upload...',
                style: 'info',
            });

            xhr.upload.addEventListener('progress', (event) => {
                if (!event.lengthComputable) {
                    return;
                }

                const percent = Math.min(100, Math.round((event.loaded / event.total) * 100));
                setProgressState({
                    title: 'Uploading file...',
                    percent,
                    note: `${percent}% uploaded. Please wait while we prepare the preview.`,
                    style: 'info',
                });
            });
        } else {
            setProgressState({
                title: options.processingTitle,
                percent: 100,
                note: options.processingNote,
                style: 'warning',
                indeterminate: true,
            });
        }

        xhr.onreadystatechange = function () {
            if (xhr.readyState !== XMLHttpRequest.DONE) {
                return;
            }

            let payload = null;

            try {
                payload = xhr.responseText ? JSON.parse(xhr.responseText) : null;
            } catch (error) {
                payload = null;
            }

            if (xhr.status >= 200 && xhr.status < 300) {
                if (options.uploadProgress) {
                    setProgressState({
                        title: 'Upload complete',
                        percent: 100,
                        note: 'Preview is ready. Redirecting...',
                        style: 'success',
                    });
                    showImportToast('success', 'Preview ready', 'The file preview was generated successfully.');
                } else {
                    setProgressState({
                        title: 'Import complete',
                        percent: 100,
                        note: payload?.message || 'Import finished successfully. Redirecting...',
                        style: 'success',
                    });
                    showImportToast('success', 'Import complete', payload?.message || 'Import finished successfully.');
                }

                window.setTimeout(() => redirectToResponse(payload), 350);
                return;
            }

            hideProgressState();
            showAjaxError(firstErrorMessage(payload, 'Something went wrong. Please try again.'));
        };

            xhr.onerror = function () {
                hideProgressState();
                showAjaxError('Network error. Please check the connection and try again.');
            };

        xhr.send(formData);
    }

    function runImportBatches() {
        if (!confirmImportForm) return;

        hideAjaxError();
        const submitButton = confirmImportForm.querySelector('button[type="submit"]');
        const clearButton = clearImportForm?.querySelector('button[type="submit"]');

        const sendNextBatch = () => {
            const xhr = new XMLHttpRequest();
            const formData = new FormData(confirmImportForm);

            xhr.open(confirmImportForm.method || 'POST', confirmImportForm.action, true);
            xhr.setRequestHeader('Accept', 'application/json');
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

            xhr.onreadystatechange = function () {
                if (xhr.readyState !== XMLHttpRequest.DONE) {
                    return;
                }

                let payload = null;

                try {
                    payload = xhr.responseText ? JSON.parse(xhr.responseText) : null;
                } catch (error) {
                    payload = null;
                }

                if (xhr.status < 200 || xhr.status >= 300) {
                    hideProgressState();
                    if (submitButton) {
                        submitButton.disabled = false;
                        submitButton.innerHTML = '<i class="bi bi-database-check me-1"></i>Import All Valid Rows';
                    }
                    if (clearButton) {
                        clearButton.disabled = false;
                    }
                    showAjaxError(firstErrorMessage(payload, 'Import failed while processing a batch. Please try again.'));
                    return;
                }

                const progress = payload?.progress || {};
                const percent = Number.isFinite(progress.percent) ? progress.percent : 0;
                const currentBatch = progress.current_batch ?? 0;
                const totalBatches = progress.total_batches ?? 0;
                const processedRows = progress.processed_rows ?? 0;
                const totalRows = progress.total_rows ?? 0;
                const importedRows = progress.imported_rows ?? 0;
                const skippedRows = progress.skipped_rows ?? 0;

                setProgressState({
                    title: payload?.complete ? 'Import complete' : `Importing batch ${currentBatch} of ${totalBatches}`,
                    percent,
                    note: `${processedRows} of ${totalRows} rows processed. Imported ${importedRows}, skipped ${skippedRows}.`,
                    style: payload?.complete ? 'success' : 'warning',
                    indeterminate: false,
                });

                if (payload?.complete) {
                    window.setTimeout(() => redirectToResponse(payload), 500);
                    return;
                }

                window.setTimeout(sendNextBatch, 150);
            };

            xhr.onerror = function () {
                hideProgressState();
                if (submitButton) submitButton.disabled = false;
                if (clearButton) clearButton.disabled = false;
                showAjaxError('Network error while processing import batches. Please try again.');
            };

            xhr.send(formData);
        };

        setProgressState({
            title: 'Starting import...',
            percent: 0,
            note: 'Preparing the first database batch.',
            style: 'warning',
            indeterminate: false,
        });

        if (submitButton) {
            submitButton.disabled = true;
            submitButton.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Importing...';
        }

        if (clearButton) {
            clearButton.disabled = true;
        }

        if (inlineProgressPanel) {
            inlineProgressPanel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } else if (progressPanel) {
            progressPanel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        sendNextBatch();
    }

    if (dropZone && fileInput) {
        dropZone.addEventListener('click', () => fileInput.click());

        fileInput.addEventListener('change', function () {
            if (this.files[0]) {
                fileName.textContent = this.files[0].name;
                fileInfo.style.display = 'block';
            }
        });

        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });

        dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));

        dropZone.addEventListener('drop', function (e) {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            fileInput.files = e.dataTransfer.files;

            if (fileInput.files[0]) {
                fileName.textContent = fileInput.files[0].name;
                fileInfo.style.display = 'block';
            }
        });
    }

    if (previewImportForm) {
        previewImportForm.addEventListener('submit', function (event) {
            event.preventDefault();
            submitWithAjax(previewImportForm, { uploadProgress: true });
        });
    }

    if (confirmImportForm) {
        confirmImportForm.addEventListener('submit', function (event) {
            event.preventDefault();
            runImportBatches();
        });
    }

    if (clearImportForm) {
        clearImportForm.addEventListener('submit', function () {
            hideAjaxError();
            setProgressState({
                title: 'Clearing import...',
                percent: 100,
                note: 'Removing the current preview.',
                style: 'secondary',
                indeterminate: true,
            });
        });
    }

    @if(session('success'))
        showImportToast('success', 'Import notification', @json(session('success')));
    @endif

    @if($errors->any())
        showImportToast('danger', 'Import notification', @json($errors->first()));
    @endif
</script>
@endpush
