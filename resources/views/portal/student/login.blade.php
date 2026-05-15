@php $errors = $errors ?? new \Illuminate\Support\ViewErrorBag; @endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Student Login — EduSystem</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
<style>
*{box-sizing:border-box;margin:0;padding:0;}
body{min-height:100vh;font-family:'Inter',sans-serif;background:linear-gradient(135deg,#0f4c75 0%,#1b6ca8 40%,#0d7377 100%);display:flex;align-items:center;justify-content:center;position:relative;overflow:hidden;}
body::before{content:'';position:absolute;inset:0;background:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");}
.card{background:#fff;border-radius:24px;padding:48px 44px;width:100%;max-width:440px;box-shadow:0 32px 80px rgba(0,0,0,.25);position:relative;z-index:1;}
.brand{text-align:center;margin-bottom:32px;}
.brand-icon{width:72px;height:72px;background:linear-gradient(135deg,#0f4c75,#0d7377);border-radius:20px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;box-shadow:0 8px 24px rgba(15,76,117,.4);}
.brand-icon i{font-size:32px;color:#fff;}
.brand h1{font-size:1.6rem;font-weight:800;color:#1a1a2e;letter-spacing:-0.5px;}
.brand p{color:#6b7280;font-size:.875rem;margin-top:4px;}
.form-group{margin-bottom:20px;}
.form-group label{display:block;font-size:.8rem;font-weight:600;color:#374151;margin-bottom:8px;text-transform:uppercase;letter-spacing:.5px;}
.input-wrap{position:relative;}
.input-wrap i{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#9ca3af;font-size:1rem;}
.input-wrap input{width:100%;padding:13px 14px 13px 42px;border:2px solid #e5e7eb;border-radius:12px;font-family:'Inter',sans-serif;font-size:.925rem;color:#1f2937;transition:border-color .2s,box-shadow .2s;outline:none;}
.input-wrap input.has-toggle{padding-right:44px;}
.input-wrap input:focus{border-color:#0d7377;box-shadow:0 0 0 4px rgba(13,115,119,.1);}
.toggle-pw{position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;color:#9ca3af;cursor:pointer;font-size:1rem;padding:0;line-height:1;}
.btn-login{width:100%;padding:14px;background:linear-gradient(135deg,#0f4c75,#0d7377);color:#fff;border:none;border-radius:12px;font-family:'Inter',sans-serif;font-size:1rem;font-weight:700;cursor:pointer;transition:opacity .2s,transform .1s;margin-top:8px;}
.btn-login:hover{opacity:.92;transform:translateY(-1px);}
.alert{padding:12px 16px;border-radius:10px;font-size:.85rem;margin-bottom:20px;display:flex;align-items:center;gap:8px;}
.alert-error{background:#fef2f2;border:1px solid #fecaca;color:#dc2626;}
.alert-success{background:#f0fdf4;border:1px solid #bbf7d0;color:#16a34a;}
.divider{text-align:center;color:#9ca3af;font-size:.8rem;margin:20px 0;}
.teacher-link{display:block;text-align:center;color:#0d7377;font-size:.875rem;font-weight:600;text-decoration:none;transition:color .2s;}
.teacher-link:hover{color:#0f4c75;}
.badge-class{display:inline-block;background:linear-gradient(135deg,#0f4c75,#0d7377);color:#fff;padding:2px 10px;border-radius:20px;font-size:.7rem;font-weight:700;margin-left:6px;}
</style>
</head>
<body>
<div class="card">
  <div class="brand">
    <div class="brand-icon"><i class="bi bi-mortarboard-fill"></i></div>
    <h1>Student Portal</h1>
    <p>Sign in with your school email address</p>
  </div>

  @if(session('error'))
  <div class="alert alert-error"><i class="bi bi-exclamation-circle-fill"></i>{{ session('error') }}</div>
  @endif
  @if(session('success'))
  <div class="alert alert-success"><i class="bi bi-check-circle-fill"></i>{{ session('success') }}</div>
  @endif

  <form action="{{ route('student.login.post') }}" method="POST">
    @csrf
    <div class="form-group">
      <label>Email Address</label>
      <div class="input-wrap">
        <i class="bi bi-envelope-fill"></i>
        <input type="email" name="email" value="{{ old('email') }}" placeholder="your.email@school.com" required autofocus>
      </div>
      @error('email')<div style="color:#dc2626;font-size:.78rem;margin-top:5px;">{{ $message }}</div>@enderror
    </div>
    <div class="form-group">
      <label>Password</label>
      <div class="input-wrap">
        <i class="bi bi-lock-fill"></i>
        <input type="password" name="password" id="passwordInput" class="has-toggle" placeholder="••••••••" required>
        <button type="button" class="toggle-pw" onclick="togglePw()" id="toggleBtn"><i class="bi bi-eye" id="eyeIcon"></i></button>
      </div>
    </div>
    <button type="submit" class="btn-login"><i class="bi bi-box-arrow-in-right me-2"></i>Sign In to Portal</button>
  </form>


</div>
<script>
function togglePw(){
  const inp=document.getElementById('passwordInput');
  const icon=document.getElementById('eyeIcon');
  if(inp.type==='password'){inp.type='text';icon.className='bi bi-eye-slash';}
  else{inp.type='password';icon.className='bi bi-eye';}
}
</script>
</body>
</html>
