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
</style>
@stack('styles')
</head>
<body>

{{-- Sidebar --}}
<div class="portal-sidebar" style="background:@yield('sidebar-bg-inline','linear-gradient(180deg,#0f4c75 0%,#0d7377 100%)');">
  <div class="brand">
    <h2><i class="bi bi-mortarboard-fill me-2"></i>EduSystem</h2>
    <small>@yield('portal-type','Portal')</small>
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
    <div class="user-info text-end">
      <strong>@yield('user-name')</strong>
      <small>Class <span class="badge-class">@yield('user-class')</span> @yield('user-role','User')</small>
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
(function tick(){
  const d=new Date();
  const el=document.getElementById('live-clock');
  if(el) el.textContent=d.toLocaleDateString('en-GB',{weekday:'short',day:'2-digit',month:'short',year:'numeric'})+' · '+d.toLocaleTimeString('en-GB',{hour:'2-digit',minute:'2-digit',second:'2-digit'});
  setTimeout(tick,1000);
})();
</script>
@stack('scripts')
</body>
</html>
