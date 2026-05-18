<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - EduSystem</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        body{min-height:100vh;background:#eef2f7;font-family:'Segoe UI',sans-serif;display:flex;align-items:center;justify-content:center;margin:0;}
        .login-shell{width:min(420px,calc(100% - 32px));background:#fff;border-radius:14px;box-shadow:0 16px 40px rgba(15,23,42,.12);overflow:hidden;}
        .login-head{background:#1a1f2e;color:#fff;padding:26px 28px;}
        .login-head h1{font-size:1.35rem;font-weight:800;margin:0;}
        .login-head small{color:#a7b0c2;}
        .login-body{padding:28px;}
        .form-label{font-size:.85rem;font-weight:700;color:#374151;}
        .form-control{border-radius:10px;padding:11px 12px;}
        .btn-admin{background:#6366f1;color:#fff;border:none;border-radius:10px;padding:11px 16px;font-weight:700;width:100%;}
        .btn-admin:hover{background:#4f46e5;color:#fff;}
    </style>
</head>
<body>
    <main class="login-shell">
        <div class="login-head">
            <h1><i class="bi bi-mortarboard-fill me-2"></i>EduSystem Admin</h1>
            <small>Sign in to manage students, teachers, subjects, imports, and exports</small>
        </div>
        <div class="login-body">
            @if(session('error'))
                <div class="alert alert-danger rounded-3 py-2">{{ session('error') }}</div>
            @endif
            @if(session('success'))
                <div class="alert alert-success rounded-3 py-2">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger rounded-3 py-2">{{ $errors->first() }}</div>
            @endif

            <form action="{{ route('admin.login.post') }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="username">Username</label>
                    <input class="form-control" id="username" name="username" value="{{ old('username') }}" autocomplete="username" autofocus>
                </div>
                <div class="mb-4">
                    <label class="form-label" for="password">Password</label>
                    <input class="form-control" id="password" name="password" type="password" autocomplete="current-password">
                </div>
                <button class="btn btn-admin" type="submit"><i class="bi bi-box-arrow-in-right me-1"></i>Sign In</button>
            </form>
        </div>
    </main>
</body>
</html>
