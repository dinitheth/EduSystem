@php
  $organizationProfile = \App\Models\OrganizationProfile::defaultProfile();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="icon" href="data:,">{{-- No favicon --}}
<title>@yield('title','Portal') — EduSystem</title>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
body{font-family:'Inter',sans-serif;background:#f1f5f9;min-height:100vh;}

/* Sidebar */
.portal-sidebar{width:240px;min-height:100vh;position:fixed;top:0;left:0;z-index:100;display:flex;flex-direction:column;}
.portal-sidebar .brand{padding:24px 20px 16px;border-bottom:1px solid rgba(255,255,255,.12);}
.portal-sidebar .brand-row{display:flex;align-items:center;gap:10px;}
.portal-sidebar .brand-logo{width:34px;height:34px;border-radius:10px;display:inline-flex;align-items:center;justify-content:center;overflow:hidden;background:rgba(255,255,255,.16);color:#fff;flex:0 0 34px;}
.portal-sidebar .brand-logo img{width:100%;height:100%;object-fit:contain;background:#fff;display:block;}
.portal-sidebar .brand h2{color:#fff;font-size:1.1rem;font-weight:800;margin:0;}
.portal-sidebar .brand small{color:rgba(255,255,255,.65);font-size:.72rem;}
.portal-sidebar .nav-link{color:rgba(255,255,255,.8);padding:11px 20px;font-size:.875rem;font-weight:500;display:flex;align-items:center;gap:10px;transition:background .15s,color .15s;text-decoration:none;}
.portal-sidebar .nav-link:hover,.portal-sidebar .nav-link.active{background:rgba(255,255,255,.14);color:#fff;}
.portal-sidebar .nav-link i{width:20px;text-align:center;font-size:1rem;}
.portal-sidebar .section-label{color:rgba(255,255,255,.4);font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;padding:14px 20px 4px;}
.portal-sidebar .sidebar-footer{margin-top:auto;padding:16px 20px;border-top:1px solid rgba(255,255,255,.1);}
.logout-btn{background:rgba(255,255,255,.12);color:rgba(255,255,255,.9);border:none;padding:8px 16px;border-radius:8px;font-size:.8rem;cursor:pointer;transition:background .15s;font-family:'Inter',sans-serif;display:flex;align-items:center;gap:6px;width:100%;justify-content:center;}
.logout-btn:hover{background:rgba(255,255,255,.22);}

/* Main */
.portal-main{margin-left:240px;min-height:100vh;}
.portal-topbar{background:#fff;border-bottom:1px solid #e5e7eb;padding:14px 28px;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;z-index:50;}
.portal-topbar .user-info strong{font-size:.9rem;color:#1f2937;}
.portal-topbar .user-info small{color:#6b7280;font-size:.75rem;display:block;}
.portal-content{padding:28px;}

/* Cards */
.hello-banner{border-radius:16px;padding:28px 32px;margin-bottom:24px;position:relative;overflow:hidden;}
.stat-card{background:#fff;border-radius:14px;padding:20px 22px;box-shadow:0 1px 4px rgba(0,0,0,.06);border:1px solid #f1f5f9;}
.stat-card .stat-icon{width:48px;height:48px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.4rem;margin-bottom:12px;}
.stat-card .stat-num{font-size:1.8rem;font-weight:800;color:#1f2937;}
.stat-card .stat-label{font-size:.78rem;color:#6b7280;font-weight:500;}
.section-card{background:#fff;border-radius:14px;box-shadow:0 1px 4px rgba(0,0,0,.06);border:1px solid #f1f5f9;overflow:hidden;margin-bottom:20px;}
.section-card .section-header{padding:16px 20px;border-bottom:1px solid #f1f5f9;display:flex;justify-content:space-between;align-items:center;}
.section-card .section-header h5{font-size:.95rem;font-weight:700;color:#1f2937;margin:0;}
.badge-class{background:#e0f2fe;color:#0369a1;padding:3px 10px;border-radius:20px;font-size:.7rem;font-weight:700;letter-spacing:.5px;}
.subject-chip{background:#eef2ff;color:#6366f1;padding:4px 12px;border-radius:20px;font-size:.75rem;font-weight:600;display:inline-block;margin:2px;}
.notification-btn{width:38px;height:38px;border:1px solid #e5e7eb;background:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#475569;position:relative;}
.notification-btn:hover{background:#f8fafc;color:#0f172a;}
.notification-badge{position:absolute;top:-4px;right:-4px;min-width:18px;height:18px;border-radius:999px;background:#dc2626;color:#fff;font-size:.62rem;font-weight:800;display:flex;align-items:center;justify-content:center;padding:0 5px;border:2px solid #fff;}
.notification-menu{width:340px;max-height:420px;overflow:auto;border:none;border-radius:12px;box-shadow:0 18px 45px rgba(15,23,42,.16);padding:0;}
.notification-item{display:block;text-decoration:none;color:#111827;padding:12px 14px;border-bottom:1px solid #f1f5f9;}
.notification-item:hover{background:#f8fafc;color:#111827;}
.notification-item.unread{background:#eff6ff;}
.notification-item-title{font-size:.82rem;font-weight:800;margin-bottom:3px;}
.notification-item-body{font-size:.74rem;color:#64748b;line-height:1.35;}
.notification-item-time{font-size:.68rem;color:#94a3b8;margin-top:5px;}
.portal-confirm-icon{width:48px;height:48px;border-radius:14px;background:#eef2ff;color:#4338ca;display:flex;align-items:center;justify-content:center;font-size:1.35rem;flex-shrink:0;}
.portal-confirm-title{font-size:1rem;font-weight:800;color:#111827;margin:0;}
.portal-confirm-message{font-size:.88rem;color:#64748b;margin:4px 0 0;line-height:1.45;}
</style>
@stack('styles')
</head>
<body>

{{-- Sidebar --}}
<div class="portal-sidebar" style="background:@yield('sidebar-bg-inline','linear-gradient(180deg,#0f4c75 0%,#0d7377 100%)');">
  <div class="brand">
    <div class="brand-row">
      <span class="brand-logo">
        @if($organizationProfile->logo_path)
          <img src="{{ asset('storage/'.$organizationProfile->logo_path) }}" alt="{{ $organizationProfile->organization_name }} logo">
        @else
          <i class="bi bi-mortarboard-fill"></i>
        @endif
      </span>
      <div>
        <h2>{{ $organizationProfile->organization_name }}</h2>
        <small>@yield('portal-type','Portal')</small>
      </div>
    </div>
  </div>
  @yield('sidebar-nav')
  <div class="sidebar-footer">
    <form action="@yield('logout-route','/')" method="POST">
      @csrf
      <button type="submit" class="logout-btn"><i class="bi bi-box-arrow-left"></i>Sign Out</button>
    </form>
  </div>
</div>

{{-- Main Content --}}
<div class="portal-main">
  <div class="portal-topbar">
    <div class="d-flex flex-column">
      <span style="font-size:.8rem;color:#9ca3af;">@yield('breadcrumb','Dashboard')</span>
      <span id="live-clock" style="font-size:.72rem;color:#b0b7c3;margin-top:1px;"></span>
    </div>
    <div class="d-flex align-items-center gap-3">
      <div class="dropdown">
        <button class="notification-btn" type="button" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false" title="Notifications">
          <i class="bi bi-bell-fill"></i>
          @if(($portalUnreadNotifications ?? 0) > 0)
            <span class="notification-badge">{{ $portalUnreadNotifications > 9 ? '9+' : $portalUnreadNotifications }}</span>
          @endif
        </button>
        <div class="dropdown-menu dropdown-menu-end notification-menu">
          <div class="px-3 py-2 border-bottom d-flex justify-content-between align-items-center">
            <strong style="font-size:.86rem;">Notifications</strong>
            @if(($portalUnreadNotifications ?? 0) > 0)
              <span class="badge rounded-pill text-bg-danger">{{ $portalUnreadNotifications }} new</span>
            @endif
          </div>
          @forelse(($portalNotifications ?? collect()) as $notification)
            <a href="{{ route($notification->recipient_type === 'teacher' ? 'teacher.notifications.open' : 'student.notifications.open', $notification->id) }}" class="notification-item {{ $notification->read_at ? '' : 'unread' }}">
              <div class="notification-item-title">
                @if(!$notification->read_at)<i class="bi bi-circle-fill me-1" style="font-size:.45rem;color:#2563eb;"></i>@endif
                {{ $notification->title }}
              </div>
              @if($notification->body)
                <div class="notification-item-body">{{ $notification->body }}</div>
              @endif
              <div class="notification-item-time">{{ $notification->created_at->diffForHumans() }}</div>
            </a>
          @empty
            <div class="px-3 py-4 text-center text-muted" style="font-size:.8rem;">No notifications yet.</div>
          @endforelse
        </div>
      </div>
      <div class="user-info text-end">
        <strong>@yield('user-name')</strong>
        <small>Class <span class="badge-class">@yield('user-class')</span> @yield('user-role','User')</small>
      </div>
    </div>
  </div>
  <div class="portal-content">
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-3" role="alert">
      <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-3" role="alert">
      <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @yield('content')
  </div>
</div>

<div class="modal fade" id="portalConfirmModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" style="max-width:420px;">
    <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">
      <div class="modal-body p-4">
        <div class="d-flex gap-3">
          <div class="portal-confirm-icon" id="portalConfirmIcon"><i class="bi bi-question-circle-fill"></i></div>
          <div>
            <h5 class="portal-confirm-title" id="portalConfirmTitle">Confirm Action</h5>
            <p class="portal-confirm-message" id="portalConfirmMessage">Are you sure you want to continue?</p>
          </div>
        </div>
      </div>
      <div class="modal-footer border-0 pt-0 px-4 pb-4 gap-2">
        <button type="button" class="btn btn-outline-secondary px-4" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn px-4 fw-semibold" id="portalConfirmYes" style="background:#4338ca;color:#fff;">Confirm</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function tick(){
  const d=new Date();
  const el=document.getElementById('live-clock');
  if(el) el.textContent=d.toLocaleDateString('en-GB',{weekday:'short',day:'2-digit',month:'short',year:'numeric'})+' · '+d.toLocaleTimeString('en-GB',{hour:'2-digit',minute:'2-digit',second:'2-digit'});
  setTimeout(tick,1000);
})();

window.portalConfirm = function(options = {}) {
  const modalEl = document.getElementById('portalConfirmModal');
  const titleEl = document.getElementById('portalConfirmTitle');
  const messageEl = document.getElementById('portalConfirmMessage');
  const confirmBtn = document.getElementById('portalConfirmYes');
  const iconEl = document.getElementById('portalConfirmIcon');
  const modal = bootstrap.Modal.getOrCreateInstance(modalEl);

  titleEl.textContent = options.title || 'Confirm Action';
  messageEl.textContent = options.message || 'Are you sure you want to continue?';
  confirmBtn.textContent = options.confirmText || 'Confirm';
  confirmBtn.style.background = options.danger ? '#dc2626' : '#4338ca';
  iconEl.style.background = options.danger ? '#fee2e2' : '#eef2ff';
  iconEl.style.color = options.danger ? '#dc2626' : '#4338ca';
  iconEl.innerHTML = options.danger ? '<i class="bi bi-exclamation-triangle-fill"></i>' : '<i class="bi bi-question-circle-fill"></i>';

  return new Promise((resolve) => {
    const cleanup = () => {
      confirmBtn.removeEventListener('click', onConfirm);
      modalEl.removeEventListener('hidden.bs.modal', onHidden);
    };
    const onConfirm = () => {
      cleanup();
      modal.hide();
      resolve(true);
    };
    const onHidden = () => {
      cleanup();
      resolve(false);
    };
    confirmBtn.addEventListener('click', onConfirm, { once: true });
    modalEl.addEventListener('hidden.bs.modal', onHidden, { once: true });
    modal.show();
  });
};

document.addEventListener('submit', async function(event) {
  const form = event.target.closest('form[data-confirm-message]');
  if (!form || form.dataset.confirmed === '1') return;

  event.preventDefault();
  const ok = await window.portalConfirm({
    title: form.dataset.confirmTitle || 'Confirm Action',
    message: form.dataset.confirmMessage,
    confirmText: form.dataset.confirmText || 'Confirm',
    danger: form.dataset.confirmDanger === 'true',
  });

  if (ok) {
    form.dataset.confirmed = '1';
    form.requestSubmit ? form.requestSubmit() : form.submit();
  }
});
</script>
@stack('scripts')
</body>
</html>
