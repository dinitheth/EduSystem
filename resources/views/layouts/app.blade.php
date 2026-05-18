<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Student Registration System')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body { margin: 0; font-family: 'Segoe UI', sans-serif; background: #f0f4f8; }

        /* ── Sidebar ── */
        #sidebar {
            width: 240px;
            min-height: 100vh;
            background: #1a1f2e;
            position: fixed;
            top: 0; left: 0;
            display: flex;
            flex-direction: column;
            z-index: 100;
        }
        .sidebar-brand {
            padding: 22px 20px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .sidebar-brand h6 {
            color: #fff;
            font-weight: 700;
            font-size: 1rem;
            margin: 0;
            letter-spacing: 0.3px;
        }
        .sidebar-brand small { color: #8a94a6; font-size: 0.72rem; }
        .sidebar-nav { padding: 14px 0; flex: 1; }
        .nav-label {
            color: #8a94a6;
            font-size: 0.68rem;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 10px 20px 4px;
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 10px 20px;
            color: #b0b8c9;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            border-left: 3px solid transparent;
            transition: all 0.18s;
        }
        .sidebar-link:hover {
            background: rgba(255,255,255,0.05);
            color: #fff;
        }
        .sidebar-link.active {
            background: rgba(99,102,241,0.15);
            color: #818cf8;
            border-left-color: #6366f1;
        }
        .sidebar-link i { font-size: 1.05rem; width: 18px; }

        /* ── Main content ── */
        #main {
            margin-left: 240px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .topbar {
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            padding: 14px 28px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .topbar h5 { margin: 0; font-weight: 600; font-size: 1rem; color: #1a1f2e; }
        .topbar small { color: #6b7280; font-size: 0.8rem; }
        .content { padding: 28px; flex: 1; }

        /* ── Cards ── */
        .card {
            border: none;
            border-radius: 14px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.07);
        }
        .card-header {
            border-radius: 14px 14px 0 0 !important;
            font-weight: 600;
            font-size: 0.95rem;
        }

        /* ── Stat cards ── */
        .stat-card {
            border: none;
            border-radius: 16px;
            padding: 28px 24px;
            cursor: pointer;
            transition: transform 0.18s, box-shadow 0.18s;
            text-decoration: none;
            display: block;
        }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 12px 30px rgba(0,0,0,0.12); }
        .stat-card .stat-icon {
            width: 52px; height: 52px;
            border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 16px;
        }
        .stat-card .stat-num { font-size: 2.2rem; font-weight: 700; line-height: 1; }
        .stat-card .stat-label { font-size: 0.85rem; margin-top: 4px; opacity: 0.75; }

        /* ── Table ── */
        .table thead th {
            background-color: #1a1f2e;
            color: #fff;
            font-weight: 500;
            font-size: 0.82rem;
            white-space: nowrap;
            position: sticky; top: 0; z-index: 1;
        }
        .table tbody tr:hover { background: #f8f9fa; }
        .badge-code {
            background: #eef2ff;
            color: #6366f1;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 0.79rem;
        }
        .form-label { font-weight: 500; font-size: 0.875rem; color: #374151; }
        .btn-action { padding: 3px 10px; font-size: 0.78rem; border-radius: 6px; }
        .empty-state { padding: 48px 20px; text-align: center; color: #9ca3af; }
        .empty-state i { font-size: 2.8rem; display: block; margin-bottom: 10px; }
    </style>
</head>
<body>

{{-- ══ SIDEBAR ══ --}}
<div id="sidebar">
    <div class="sidebar-brand">
        <h6><i class="bi bi-mortarboard-fill me-2" style="color:#818cf8;"></i>EduSystem</h6>
        <small>School Management</small>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-label">Main Menu</div>

        <a href="{{ route('dashboard') }}"
           class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>

        <a href="{{ route('students.index') }}"
           class="sidebar-link {{ request()->routeIs('students.*') ? 'active' : '' }}">
            <i class="bi bi-people-fill"></i> Students
        </a>

        <a href="{{ route('teachers.index') }}"
           class="sidebar-link {{ request()->routeIs('teachers.*') ? 'active' : '' }}">
            <i class="bi bi-person-workspace"></i> Teachers
        </a>

        <a href="{{ route('subjects.index') }}"
           class="sidebar-link {{ request()->routeIs('subjects.*') ? 'active' : '' }}">
            <i class="bi bi-book-fill"></i> Subjects
        </a>

        <div style="flex:1;"></div>

        <div class="nav-label" style="margin-top:10px;">Data Tools</div>

        <a href="{{ route('import.index') }}"
           class="sidebar-link {{ request()->routeIs('import.*') ? 'active' : '' }}">
            <i class="bi bi-box-arrow-in-down"></i> Import
        </a>
    </nav>
</div>

{{-- ══ MAIN ══ --}}
<div id="main">
    <div class="topbar">
        <div>
            <h5>@yield('page-title', 'Dashboard')</h5>
            <small>@yield('page-subtitle', 'Welcome back!')</small>
        </div>
        <div class="ms-auto d-flex align-items-center gap-3">
            <span style="font-size:.85rem;color:#374151;font-weight:600;">{{ session('admin_name', 'Admin') }}</span>
            <form action="{{ route('admin.logout') }}" method="POST" class="m-0">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;font-size:.78rem;">
                    <i class="bi bi-box-arrow-left me-1"></i>Sign Out
                </button>
            </form>
        </div>
    </div>

    <div class="content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')

{{-- ══ Global Delete Confirmation Modal ══ --}}
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width:380px;">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 pb-0" style="background:#fff1f2;">
                <div class="d-flex align-items-center gap-3 w-100 pt-1">
                    <div style="width:46px;height:46px;border-radius:50%;background:#fee2e2;display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                        <i class="bi bi-trash3-fill text-danger" style="font-size:1.2rem;"></i>
                    </div>
                    <div>
                        <h6 class="mb-0 fw-700" style="font-size:1rem;color:#111827;">Delete Record</h6>
                        <small class="text-muted">This action cannot be undone</small>
                    </div>
                </div>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="modal" style="position:absolute;top:14px;right:16px;"></button>
            </div>
            <div class="modal-body pt-3 pb-2 px-4" style="background:#fff1f2;">
                <p class="mb-0 text-secondary" style="font-size:0.9rem;">
                    Are you sure you want to delete
                    <strong id="deleteItemName" class="text-dark"></strong>?
                </p>
            </div>
            <div class="modal-footer border-0 px-4 pb-4 pt-3 gap-2" style="background:#fff;">
                <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-1"></i>Cancel
                </button>
                <button type="button" class="btn btn-danger px-4 fw-semibold" id="confirmDeleteBtn">
                    <i class="bi bi-trash3 me-1"></i>Yes, Delete
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    // Global delete confirmation handler
    // Usage: add class="delete-trigger" + data-name="..." + data-form="formId" to any button
    let deleteTargetForm = null;

    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.delete-trigger');
        if (!btn) return;

        e.preventDefault();
        deleteTargetForm = document.getElementById(btn.dataset.form);
        document.getElementById('deleteItemName').textContent = btn.dataset.name || 'this record';

        new bootstrap.Modal(document.getElementById('deleteConfirmModal')).show();
    });

    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (deleteTargetForm) deleteTargetForm.submit();
    });
</script>
</body>
</html>
