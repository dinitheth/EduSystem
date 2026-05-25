@extends('layouts.app')
@section('title','Setup')
@section('page-title','Setup')
@section('page-subtitle','Configure organization branding, portals, dashboards, modules, and workflows')

@section('content')
@php
    $savedWorkflow = json_decode($profile->custom_css ?: '{}', true);
    $activeWorkflow = old('workflow_controls', $savedWorkflow['workflow_controls'] ?? []);
    $activeModules = old('enabled_modules', $profile->enabled_modules ?? []);
    $activeWidgets = old('dashboard_widgets', $profile->dashboard_widgets ?? []);
@endphp

<style>
    .setup-card { border-radius: 14px; border: 1px solid #e5edf7; box-shadow: 0 14px 34px rgba(15,23,42,.06); }
    .setup-section-title { font-size: .92rem; font-weight: 800; color: #111827; margin-bottom: .75rem; }
    .setup-help { color:#64748b; font-size:.82rem; line-height:1.55; }
    .setup-check {
        display:flex; gap:.65rem; align-items:flex-start; padding:.8rem .85rem; border:1px solid #dbe7f4;
        border-radius:10px; background:#fff; min-height:56px; transition:.16s ease;
    }
    .setup-check:hover { border-color:#93c5fd; box-shadow:0 8px 18px rgba(37,99,235,.08); }
    .setup-check input { margin-top:.18rem; }
    .setting-category {
        border:1px solid #dbe7f4;
        border-radius:14px;
        background:#f8fbff;
        padding:14px;
        height:100%;
    }
    .setting-category-title {
        display:flex;
        align-items:center;
        gap:8px;
        margin-bottom:12px;
        color:#0f172a;
        font-size:.86rem;
        font-weight:850;
    }
    .setting-category-title i {
        width:26px;
        height:26px;
        border-radius:8px;
        display:grid;
        place-items:center;
        background:#e0f2fe;
        color:#2563eb;
    }
    .compact-check {
        min-height:0;
        padding:.62rem .7rem;
        border-radius:10px;
        font-size:.84rem;
        background:#fff;
    }
    .portal-card {
        border:1px solid #dbe7f4;
        border-radius:14px;
        padding:14px;
        height:100%;
        background:#fff;
    }
    .portal-card h6 {
        display:flex;
        align-items:center;
        gap:8px;
        margin-bottom:12px;
        font-size:.9rem;
    }
    .portal-list { display:grid; gap:.5rem; }
    .portal-item { display:flex; align-items:center; gap:.55rem; padding:.62rem .7rem; border-radius:9px; background:#f8fbff; border:1px solid #e2edf8; font-size:.86rem; color:#334155; }
    .portal-item i { color:#2563eb; }
    .color-chip { width:34px; height:34px; border-radius:10px; border:1px solid rgba(15,23,42,.12); }
</style>

<form action="{{ route('organization-settings.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')

    <div class="row g-4">
        <div class="col-xl-4">
            <div class="card setup-card h-100">
                <div class="card-header text-white py-3" style="background:linear-gradient(135deg,{{ $profile->primary_color }},{{ $profile->secondary_color }});">
                    <i class="bi bi-sliders2-vertical me-2"></i>Current Setup
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div id="previewLogoBox" style="width:68px;height:68px;border-radius:18px;background:{{ $profile->primary_color }};display:grid;place-items:center;color:#fff;overflow:hidden;">
                            @if($profile->logo_path)
                                <img id="previewLogoImage" src="{{ asset('storage/'.$profile->logo_path) }}" alt="Logo" style="width:100%;height:100%;object-fit:contain;background:#fff;">
                            @else
                                <i id="previewLogoFallback" class="bi bi-mortarboard-fill" style="font-size:1.8rem;"></i>
                            @endif
                        </div>
                        <div>
                            <h5 class="mb-1" id="previewOrganizationName">{{ $profile->organization_name }}</h5>
                            <span class="badge rounded-pill" style="background:#eef2ff;color:#4f46e5;">Organization profile</span>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mb-3">
                        <span class="color-chip" id="previewPrimaryColor" style="background:{{ $profile->primary_color }};"></span>
                        <span class="color-chip" id="previewSecondaryColor" style="background:{{ $profile->secondary_color }};"></span>
                        <span class="color-chip" id="previewAccentColor" style="background:{{ $profile->accent_color }};"></span>
                    </div>

                    <div class="alert rounded-3 mb-3" style="background:#f0f9ff;border:1px solid #bae6fd;color:#075985;">
                        <strong>System setup profile</strong>
                        <div class="small mt-1">Brand identity, subscription, active modules, dashboard widgets, and workflow controls are stored here for organization-level customization.</div>
                    </div>

                    <div class="list-group small mb-3">
                        <div class="list-group-item d-flex justify-content-between"><span>Subscription</span><strong id="previewPlan">{{ $profile->planLabel() }} Plan</strong></div>
                        <div class="list-group-item d-flex justify-content-between"><span>Plan status</span><strong>{{ $profile->planStatusLabel() }}</strong></div>
                        <div class="list-group-item d-flex justify-content-between"><span>Modules enabled</span><strong id="previewModuleCount">{{ count($profile->enabled_modules ?? []) }}</strong></div>
                        <div class="list-group-item d-flex justify-content-between"><span>Widgets enabled</span><strong id="previewWidgetCount">{{ count($profile->dashboard_widgets ?? []) }}</strong></div>
                        <div class="list-group-item d-flex justify-content-between"><span>Workflow controls</span><strong id="previewWorkflowCount">{{ count($activeWorkflow) }}</strong></div>
                        <div class="list-group-item"><span class="text-muted">Contact:</span> <strong id="previewContact">{{ $profile->contact_email ?: 'No email set' }}</strong></div>
                        <div class="list-group-item"><span class="text-muted">Phone:</span> <strong id="previewPhone">{{ $profile->contact_phone ?: 'No phone set' }}</strong></div>
                    </div>

                    <div class="setup-help">
                        This page is the control center for making the system fit an organization. It stores what modules, dashboard cards, workflow rules, branding, and portal sections should be active.
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-8">
            <div class="card setup-card">
                <div class="card-header text-white py-3" style="background:#111827;">
                    <i class="bi bi-gear-wide-connected me-2"></i>System Configuration
                </div>
                <div class="card-body p-4">
                    @if($errors->any())
                        <div class="alert alert-danger rounded-3">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-4">
                        <div class="setup-section-title"><i class="bi bi-palette me-2 text-primary"></i>Brand and Portal Identity</div>
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Organization Name</label>
                                <input type="text" name="organization_name" class="form-control" value="{{ old('organization_name', $profile->organization_name) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Primary Color</label>
                                <input type="color" name="primary_color" class="form-control form-control-color w-100" value="{{ old('primary_color', $profile->primary_color) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Secondary Color</label>
                                <input type="color" name="secondary_color" class="form-control form-control-color w-100" value="{{ old('secondary_color', $profile->secondary_color) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Accent Color</label>
                                <input type="color" name="accent_color" class="form-control form-control-color w-100" value="{{ old('accent_color', $profile->accent_color) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Logo</label>
                                <input type="file" name="logo" id="logoInput" class="form-control" accept="image/*">
                                <div class="form-text">Required logo size: exactly 512 x 512 px. Large, wide, or small logos are rejected.</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Contact Email</label>
                                <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $profile->contact_email) }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Contact Phone</label>
                                <input type="text" name="contact_phone" class="form-control" value="{{ old('contact_phone', $profile->contact_phone) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">Address</label>
                                <input type="text" name="address" class="form-control" value="{{ old('address', $profile->address) }}">
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="setup-section-title"><i class="bi bi-toggles2 me-2 text-primary"></i>System Modules</div>
                        <div class="row g-3">
                            @foreach($modules as $group => $items)
                                <div class="col-lg-4">
                                    <div class="setting-category">
                                        <div class="setting-category-title"><i class="bi bi-folder-check"></i>{{ $group }}</div>
                                        <div class="d-grid gap-2">
                                            @foreach($items as $key => $label)
                                                <label class="setup-check compact-check">
                                                    <input type="checkbox" class="form-check-input" name="enabled_modules[]" value="{{ $key }}" @checked(in_array($key, $activeModules))>
                                                    <span>{{ $label }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="setup-section-title"><i class="bi bi-layout-three-columns me-2 text-primary"></i>Dashboard Widgets</div>
                        <div class="row g-3">
                            @foreach($widgets as $group => $items)
                                <div class="col-lg-6">
                                    <div class="setting-category">
                                        <div class="setting-category-title"><i class="bi bi-columns-gap"></i>{{ $group }}</div>
                                        <div class="d-grid gap-2">
                                            @foreach($items as $key => $label)
                                                <label class="setup-check compact-check">
                                                    <input type="checkbox" class="form-check-input" name="dashboard_widgets[]" value="{{ $key }}" @checked(in_array($key, $activeWidgets))>
                                                    <span>{{ $label }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="setup-section-title"><i class="bi bi-diagram-3 me-2 text-primary"></i>Workflow Controls</div>
                        <div class="row g-3">
                            @foreach($workflowControls as $group => $items)
                                <div class="col-lg-6">
                                    <div class="setting-category">
                                        <div class="setting-category-title"><i class="bi bi-diagram-3"></i>{{ $group }}</div>
                                        <div class="d-grid gap-2">
                                            @foreach($items as $key => $label)
                                                <label class="setup-check compact-check">
                                                    <input type="checkbox" class="form-check-input" name="workflow_controls[]" value="{{ $key }}" @checked(in_array($key, $activeWorkflow))>
                                                    <span>{{ $label }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mb-2">
                        <div class="setup-section-title"><i class="bi bi-window-sidebar me-2 text-primary"></i>Portal Sections</div>
                        <div class="row g-3">
                            @foreach($portalSections as $portal => $sections)
                                <div class="col-lg-4">
                                    <div class="portal-card">
                                        <h6 class="fw-bold text-capitalize"><i class="bi bi-window-dock text-primary"></i>{{ $portal }} Portal</h6>
                                        <div class="portal-list">
                                            @foreach($sections as $section)
                                                <div class="portal-item"><i class="bi bi-check-circle-fill"></i>{{ $section }}</div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 p-4 pt-0 text-end">
                    <button type="submit" class="btn btn-primary px-4 fw-semibold">
                        <i class="bi bi-check2-circle me-1"></i>Save Setup
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const organizationNameInput = document.querySelector('input[name="organization_name"]');
    const primaryColorInput = document.querySelector('input[name="primary_color"]');
    const secondaryColorInput = document.querySelector('input[name="secondary_color"]');
    const accentColorInput = document.querySelector('input[name="accent_color"]');
    const contactEmailInput = document.querySelector('input[name="contact_email"]');
    const contactPhoneInput = document.querySelector('input[name="contact_phone"]');
    const logoInput = document.getElementById('logoInput');

    const previewOrganizationName = document.getElementById('previewOrganizationName');
    const previewPrimaryColor = document.getElementById('previewPrimaryColor');
    const previewSecondaryColor = document.getElementById('previewSecondaryColor');
    const previewAccentColor = document.getElementById('previewAccentColor');
    const previewContact = document.getElementById('previewContact');
    const previewPhone = document.getElementById('previewPhone');
    const previewLogoBox = document.getElementById('previewLogoBox');
    const previewModuleCount = document.getElementById('previewModuleCount');
    const previewWidgetCount = document.getElementById('previewWidgetCount');
    const previewWorkflowCount = document.getElementById('previewWorkflowCount');

    function updatePreview() {
        previewOrganizationName.textContent = organizationNameInput.value.trim() || 'Organization Name';
        previewPrimaryColor.style.background = primaryColorInput.value;
        previewSecondaryColor.style.background = secondaryColorInput.value;
        previewAccentColor.style.background = accentColorInput.value;
        previewLogoBox.style.background = primaryColorInput.value;
        previewContact.textContent = contactEmailInput.value.trim() || 'No email set';
        previewPhone.textContent = contactPhoneInput.value.trim() || 'No phone set';
        previewModuleCount.textContent = document.querySelectorAll('input[name="enabled_modules[]"]:checked').length;
        previewWidgetCount.textContent = document.querySelectorAll('input[name="dashboard_widgets[]"]:checked').length;
        previewWorkflowCount.textContent = document.querySelectorAll('input[name="workflow_controls[]"]:checked').length;
    }

    document.querySelectorAll(
        'input[name="organization_name"], input[name="primary_color"], input[name="secondary_color"], input[name="accent_color"], input[name="contact_email"], input[name="contact_phone"], input[name="enabled_modules[]"], input[name="dashboard_widgets[]"], input[name="workflow_controls[]"]'
    ).forEach((input) => {
        input.addEventListener('input', updatePreview);
        input.addEventListener('change', updatePreview);
    });

    logoInput.addEventListener('change', function () {
        const file = this.files && this.files[0];
        this.setCustomValidity('');
        if (!file) {
            updatePreview();
            return;
        }

        const image = new Image();
        const url = URL.createObjectURL(file);

        image.onload = () => {
            if (image.naturalWidth !== 512 || image.naturalHeight !== 512) {
                this.value = '';
                this.setCustomValidity('Logo must be exactly 512 x 512 px.');
                this.reportValidity();
                URL.revokeObjectURL(url);
                return;
            }

            previewLogoBox.innerHTML = '';
            const img = document.createElement('img');
            img.src = url;
            img.alt = 'Logo preview';
            img.style.cssText = 'width:100%;height:100%;object-fit:contain;background:#fff;display:block;';
            previewLogoBox.appendChild(img);
            updatePreview();
        };

        image.onerror = () => {
            this.value = '';
            this.setCustomValidity('Please choose a valid image file.');
            this.reportValidity();
            URL.revokeObjectURL(url);
        };

        image.src = url;
    });

    updatePreview();
});
</script>
@endsection
