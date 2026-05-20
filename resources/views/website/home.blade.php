<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CodeXpress Institute</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root{
            --indigo:#6366f1;
            --indigo-deep:#4f46e5;
            --sky:#0ea5e9;
            --cyan:#67e8f9;
            --mint:#10b981;
            --orange:#f97316;
            --paper:#f7fbff;
            --panel:#ffffff;
            --line:#dbe5f1;
            --ink:#18233b;
            --muted:#66758f;
            --soft:#eef5ff;
            --bg-start:#f5f8ff;
            --bg-mid:#ffffff;
            --bg-end:#f9fcff;
            --nav-bg:rgba(255,255,255,.88);
            --surface-strong:#ffffff;
            --surface-soft:#f8fbff;
            --grid-line:rgba(99,102,241,.04);
            --shadow-soft:0 24px 56px rgba(15,23,42,.07);
        }
        body[data-theme="dark"]{
            --paper:#0f172a;
            --panel:#111c34;
            --line:#24324b;
            --ink:#e5eefc;
            --muted:#a6b4ca;
            --soft:#16233d;
            --bg-start:#08111f;
            --bg-mid:#0b1628;
            --bg-end:#0e1b31;
            --nav-bg:rgba(8,17,31,.84);
            --surface-strong:#0f1a30;
            --surface-soft:#111f38;
            --grid-line:rgba(103,232,249,.06);
            --shadow-soft:0 26px 60px rgba(2,6,23,.45);
        }
        *{box-sizing:border-box}
        html{scroll-behavior:smooth}
        body{
            margin:0;
            font-family:"Segoe UI",system-ui,sans-serif;
            color:var(--ink);
            background:
                radial-gradient(circle at top left, rgba(99,102,241,.18), transparent 20%),
                radial-gradient(circle at 80% 0%, rgba(14,165,233,.16), transparent 18%),
                linear-gradient(180deg,var(--bg-start) 0%,var(--bg-mid) 42%,var(--bg-end) 100%);
            transition:background .25s ease,color .25s ease;
        }
        .nav-wrap{
            position:sticky;
            top:0;
            z-index:1000;
            background:var(--nav-bg);
            backdrop-filter:blur(14px);
            border-bottom:1px solid rgba(99,102,241,.08);
        }
        .brand-mark{
            width:48px;height:48px;border-radius:15px;
            display:flex;align-items:center;justify-content:center;
            color:#fff;font-size:1.15rem;
            background:linear-gradient(135deg,var(--indigo-deep),var(--sky));
            box-shadow:0 14px 28px rgba(99,102,241,.22);
        }
        .navbar .nav-link{
            color:#42516c!important;
            font-weight:600;
            padding:.45rem .8rem!important;
        }
        .navbar .nav-link:hover{color:var(--indigo-deep)!important}
        .nav-toggle{
            width:46px;
            height:46px;
            border-radius:14px;
            border:1px solid rgba(217,229,241,.92);
            background:var(--surface-strong);
            color:var(--ink);
            display:flex;
            align-items:center;
            justify-content:center;
            box-shadow:0 10px 24px rgba(15,23,42,.05);
        }
        .theme-toggle{
            width:46px;
            height:46px;
            border-radius:14px;
            border:1px solid rgba(217,229,241,.92);
            background:var(--surface-strong);
            color:var(--ink);
            display:inline-flex;
            align-items:center;
            justify-content:center;
            box-shadow:0 10px 24px rgba(15,23,42,.05);
            transition:transform .2s ease,background .2s ease,color .2s ease,border-color .2s ease;
        }
        .theme-toggle:hover{transform:translateY(-2px)}
        .btn-brand{
            border:0;
            border-radius:16px;
            padding:12px 22px;
            font-weight:700;
            color:#fff;
            background:linear-gradient(135deg,#5448ea 0%, #5866ff 44%, #11a7e7 100%);
            box-shadow:0 16px 34px rgba(84,72,234,.24);
            transition:transform .2s ease,box-shadow .2s ease;
        }
        .btn-brand:hover{transform:translateY(-2px);box-shadow:0 20px 38px rgba(84,72,234,.28);color:#fff}
        .btn-dark-soft{
            border:0;
            border-radius:18px;
            padding:14px 24px;
            font-weight:700;
            color:#fff;
            background:#17223b;
            box-shadow:0 16px 30px rgba(23,34,59,.18);
            transition:transform .2s ease,box-shadow .2s ease;
        }
        .btn-dark-soft:hover{transform:translateY(-2px);box-shadow:0 20px 34px rgba(23,34,59,.22);color:#fff}
        .btn-ghost{
            border:1px solid rgba(99,102,241,.16);
            border-radius:18px;
            padding:14px 24px;
            font-weight:700;
            color:#42516c;
            background:var(--surface-strong);
            transition:transform .2s ease,background .2s ease,border-color .2s ease;
        }
        .btn-ghost:hover{transform:translateY(-2px);background:#f4f8ff;border-color:rgba(99,102,241,.28);color:var(--ink)}
        .hero{
            position:relative;
            padding:38px 0 42px;
        }
        .hero::before{
            content:"";
            position:absolute;
            inset:0;
            background-image:
                linear-gradient(rgba(99,102,241,.04) 1px, transparent 1px),
                linear-gradient(90deg, var(--grid-line) 1px, transparent 1px);
            background-size:46px 46px;
            pointer-events:none;
        }
        .hero-grid{
            position:relative;
            z-index:1;
            display:grid;
            grid-template-columns:1.15fr .85fr;
            gap:30px;
            align-items:start;
            padding:34px 0 18px;
        }
        .hero-copy{
            background:linear-gradient(180deg,color-mix(in srgb, var(--surface-strong) 92%, transparent),color-mix(in srgb, var(--surface-strong) 84%, transparent));
            border:1px solid rgba(217,229,241,.9);
            border-radius:34px;
            padding:34px;
            box-shadow:var(--shadow-soft);
        }
        .hero h1{
            margin:0 0 14px;
            font-size:clamp(2.8rem,6vw,5.1rem);
            line-height:.94;
            letter-spacing:-.045em;
            font-weight:800;
            max-width:11ch;
        }
        .hero-accent{
            background:linear-gradient(135deg,var(--indigo-deep) 0%, #5b67ff 24%, #349df0 70%, #59d3e8 100%);
            -webkit-background-clip:text;
            background-clip:text;
            color:transparent;
            }
        .hero p{
            margin:0;
            max-width:58ch;
            color:var(--muted);
            line-height:1.8;
            font-size:1.02rem;
        }
        .student-strip{
            margin-top:26px;
            display:flex;
            align-items:center;
            gap:14px;
            flex-wrap:wrap;
        }
        .student-avatars{
            display:flex;
            align-items:center;
        }
        .student-avatar{
            width:56px;
            height:56px;
            border-radius:18px;
            overflow:hidden;
            border:3px solid color-mix(in srgb, var(--surface-strong) 88%, transparent);
            box-shadow:0 16px 26px rgba(15,23,42,.14);
            margin-left:-12px;
            background:var(--surface-strong);
        }
        .student-avatar:first-child{margin-left:0;}
        .student-avatar img{
            width:100%;
            height:100%;
            object-fit:cover;
            display:block;
        }
        .student-strip-copy{
            min-width:220px;
        }
        .student-strip-copy strong{
            display:block;
            font-size:1rem;
        }
        .student-strip-copy span{
            color:var(--muted);
            font-size:.9rem;
        }
        .hero-actions{display:flex;gap:16px;flex-wrap:wrap;margin-top:28px}
        .hero-mini{
            margin-top:26px;
            display:grid;
            grid-template-columns:repeat(3,minmax(0,1fr));
            gap:14px;
        }
        .mini-tile{
            display:flex;
            align-items:center;
            gap:12px;
            padding:14px 16px;
            background:var(--surface-strong);
            border:1px solid rgba(217,229,241,.92);
            border-radius:18px;
            box-shadow:0 12px 24px rgba(15,23,42,.04);
            font-weight:600;
            color:var(--ink);
        }
        .mini-tile i{
            width:28px;height:28px;
            display:flex;align-items:center;justify-content:center;
            border-radius:10px;
            color:#fff;
            background:linear-gradient(135deg,var(--indigo),var(--sky));
            font-size:.82rem;
        }
        .pulse-panel{
            background:linear-gradient(180deg,var(--surface-strong),var(--surface-soft));
            border:1px solid rgba(217,229,241,.96);
            border-radius:30px;
            padding:24px;
            box-shadow:0 24px 52px rgba(15,23,42,.08);
        }
        .pulse-kicker{
            color:#12b6dd;
            font-size:.8rem;
            font-weight:800;
            letter-spacing:.08em;
            text-transform:uppercase;
        }
        .pulse-panel h3{
            margin:10px 0 8px;
            font-size:1.1rem;
            line-height:1.35;
            font-weight:800;
        }
        .pulse-panel p{
            color:#7a879c;
            line-height:1.65;
            margin:0;
        }
        .pulse-stats{
            display:grid;
            grid-template-columns:repeat(3,minmax(0,1fr));
            gap:10px;
            margin-top:18px;
        }
        .pulse-stat{
            background:var(--soft);
            border-radius:16px;
            padding:14px 12px;
            text-align:center;
            border:1px solid rgba(217,229,241,.85);
        }
        .pulse-stat strong{
            display:block;
            font-size:1.4rem;
            line-height:1.05;
            color:var(--indigo-deep);
        }
        .pulse-stat span{
            display:block;
            margin-top:4px;
            font-size:.74rem;
            color:#66758f;
            font-weight:700;
            text-transform:uppercase;
        }
        .preview-shell{
            margin-top:16px;
            border-radius:18px;
            border:1px solid rgba(217,229,241,.92);
            overflow:hidden;
            background:var(--surface-strong);
        }
        .preview-search{
            padding:12px 14px;
            border-bottom:1px solid rgba(217,229,241,.86);
            background:color-mix(in srgb, var(--surface-strong) 88%, var(--soft));
        }
        .preview-search input{
            width:100%;
            border:0;
            outline:none;
            background:transparent;
            color:var(--muted);
            font-size:.92rem;
        }
        .preview-item{
            display:flex;
            justify-content:space-between;
            align-items:center;
            gap:14px;
            padding:14px;
            border-bottom:1px solid rgba(237,242,247,.92);
        }
        .preview-item:last-child{border-bottom:0}
        .preview-item small{
            display:block;
            margin-top:2px;
            color:#9aa6b7;
        }
        .preview-item a{
            text-decoration:none;
            color:var(--indigo);
            font-size:.82rem;
            font-weight:700;
            white-space:nowrap;
        }
        .section{padding:36px 0}
        .section-head{
            display:flex;
            justify-content:space-between;
            align-items:end;
            gap:18px;
            margin-bottom:22px;
        }
        .section-kicker{
            color:var(--indigo);
            font-size:.8rem;
            font-weight:800;
            letter-spacing:.08em;
            text-transform:uppercase;
        }
        .section-head h2{
            margin:8px 0 0;
            font-size:clamp(1.85rem,4vw,3rem);
            line-height:1;
            letter-spacing:-.03em;
            font-weight:800;
        }
        .section-head p{
            margin:10px 0 0;
            max-width:58ch;
            color:var(--muted);
            line-height:1.8;
        }
        .feature-row{
            display:grid;
            grid-template-columns:repeat(4,minmax(0,1fr));
            gap:18px;
        }
        .feature-card,.course-card,.process-card{
            background:var(--surface-strong);
            border:1px solid rgba(217,229,241,.92);
            border-radius:24px;
            box-shadow:0 16px 40px rgba(15,23,42,.05);
        }
        .feature-card{
            padding:20px;
            height:100%;
        }
        .feature-card i{
            display:inline-flex;
            width:44px;height:44px;
            align-items:center;justify-content:center;
            border-radius:14px;
            color:#fff;
            font-size:1rem;
            margin-bottom:14px;
            background:linear-gradient(135deg,var(--indigo),var(--sky));
        }
        .course-shell,
        .process-shell,
        .register-shell-wrap{
            background:linear-gradient(180deg,color-mix(in srgb, var(--surface-strong) 90%, transparent),color-mix(in srgb, var(--surface-soft) 95%, transparent));
            border:1px solid rgba(217,229,241,.9);
            border-radius:32px;
            padding:26px;
            box-shadow:0 24px 56px rgba(15,23,42,.06);
        }
        .course-grid{
            display:grid;
            grid-template-columns:repeat(4,minmax(0,1fr));
            gap:18px;
        }
        .course-grid.collapsed .course-card:nth-child(n+9){
            display:none;
        }
        .course-card{
            padding:20px;
            transition:transform .2s ease,box-shadow .2s ease,border-color .2s ease;
        }
        .course-card:hover{
            transform:translateY(-5px);
            box-shadow:0 22px 48px rgba(15,23,42,.08);
            border-color:rgba(99,102,241,.26);
        }
        .course-code{
            display:inline-flex;
            border-radius:999px;
            padding:6px 10px;
            font-size:.76rem;
            font-weight:700;
            color:var(--indigo);
            background:rgba(99,102,241,.1);
        }
        .course-card h3{
            margin:14px 0 10px;
            font-size:1.18rem;
            font-weight:800;
            line-height:1.28;
        }
        .course-card p{
            margin:0;
            color:var(--muted);
            line-height:1.74;
            font-size:.93rem;
            min-height:92px;
        }
        .course-meta{
            margin-top:18px;
            display:flex;
            justify-content:space-between;
            align-items:center;
            color:#475569;
            font-size:.82rem;
            font-weight:600;
        }
        .process-grid{
            display:grid;
            grid-template-columns:repeat(5,minmax(0,1fr));
            gap:16px;
        }
        .process-card{padding:20px;min-height:188px}
        .step-badge{
            width:38px;height:38px;border-radius:12px;
            display:flex;align-items:center;justify-content:center;
            color:#fff;font-size:.88rem;font-weight:800;
            margin-bottom:14px;
            background:linear-gradient(135deg,var(--indigo-deep),var(--sky));
        }
        .register-grid{
            display:block;
        }
        .register-form{
            background:var(--surface-strong);
            border:1px solid rgba(217,229,241,.92);
            border-radius:28px;
            box-shadow:0 20px 54px rgba(15,23,42,.06);
        }
        .register-form{padding:28px}
        .form-label{
            font-size:.78rem;
            font-weight:800;
            color:var(--ink);
            letter-spacing:.05em;
            text-transform:uppercase;
            margin-bottom:8px;
        }
        .form-control,.form-select{
            border-radius:16px;
            padding:14px 15px;
            border:1px solid #d5dfeb;
            box-shadow:none!important;
            background:var(--surface-strong);
            color:var(--ink);
        }
        .form-control:focus,.form-select:focus{border-color:rgba(99,102,241,.5)}
        .suggest-shell{position:relative}
        .suggest-panel{
            position:absolute;
            top:100%;
            left:0;right:0;
            margin-top:8px;
            background:var(--surface-strong);
            border:1px solid var(--line);
            border-radius:18px;
            box-shadow:0 20px 40px rgba(15,23,42,.08);
            overflow:hidden;
            display:none;
            z-index:30;
            max-height:230px;
            overflow-y:auto;
        }
        .suggest-item{
            padding:13px 14px;
            cursor:pointer;
            border-bottom:1px solid #edf2f7;
        }
        .suggest-item:hover{background:#f8fbff}
        .chip-wrap{display:flex;flex-wrap:wrap;gap:10px}
        .subject-chip{
            display:inline-flex;
            align-items:center;
            gap:8px;
            padding:9px 12px;
            border-radius:999px;
            background:rgba(16,185,129,.1);
            color:#047857;
            font-size:.84rem;
            font-weight:700;
        }
        .subject-chip button{border:0;background:none;color:inherit;padding:0;line-height:1}
        .cta-panel{
            border-radius:30px;
            padding:32px;
            background:linear-gradient(135deg,color-mix(in srgb, var(--soft) 88%, white),color-mix(in srgb, var(--surface-strong) 90%, white));
            border:1px solid rgba(217,229,241,.92);
            box-shadow:0 24px 60px rgba(15,23,42,.06);
        }
        .view-more-wrap{
            margin-top:24px;
            text-align:center;
        }
        footer{
            padding:24px 0 46px;
            color:#64748b;
        }
        .fade-up{
            opacity:0;
            transform:translateY(24px);
            animation:fadeUp .75s ease forwards;
        }
        .d1{animation-delay:.08s}
        .d2{animation-delay:.16s}
        .d3{animation-delay:.24s}
        .d4{animation-delay:.32s}
        @keyframes fadeUp{to{opacity:1;transform:none}}
        @media (max-width: 1399.98px){
            .course-grid{grid-template-columns:repeat(3,minmax(0,1fr))}
            .feature-row{grid-template-columns:repeat(2,minmax(0,1fr))}
            .process-grid{grid-template-columns:repeat(3,minmax(0,1fr))}
        }
        @media (max-width: 991.98px){
            .hero-grid{grid-template-columns:1fr}
            .course-grid,.process-grid{grid-template-columns:repeat(2,minmax(0,1fr))}
            .hero h1{max-width:none}
            .navbar-collapse{
                margin-top:14px;
                padding:14px;
                border-radius:18px;
                background:var(--surface-strong);
                border:1px solid rgba(217,229,241,.92);
                box-shadow:0 18px 38px rgba(15,23,42,.06);
            }
            .navbar-nav{
                gap:6px;
            }
        }
        @media (max-width: 767.98px){
            .course-grid,.process-grid,.feature-row,.pulse-stats,.hero-mini{grid-template-columns:1fr}
            .section-head{display:block}
            .hero-copy,.pulse-panel,.register-form,.cta-panel,.course-shell,.process-shell,.register-shell-wrap{padding:22px}
            .hero h1{font-size:2.45rem}
            .hero{padding:38px 0 24px}
            .student-strip{align-items:flex-start}
        }
        body[data-theme="dark"] .navbar .nav-link,
        body[data-theme="dark"] .btn-ghost,
        body[data-theme="dark"] .text-muted,
        body[data-theme="dark"] .pulse-panel p,
        body[data-theme="dark"] footer,
        body[data-theme="dark"] .course-card p,
        body[data-theme="dark"] .course-meta{
            color:var(--muted)!important;
        }
        body[data-theme="dark"] .hero-copy,
        body[data-theme="dark"] .pulse-panel,
        body[data-theme="dark"] .course-shell,
        body[data-theme="dark"] .process-shell,
        body[data-theme="dark"] .register-shell-wrap,
        body[data-theme="dark"] .register-form,
        body[data-theme="dark"] .cta-panel,
        body[data-theme="dark"] .feature-card,
        body[data-theme="dark"] .course-card,
        body[data-theme="dark"] .process-card,
        body[data-theme="dark"] .mini-tile,
        body[data-theme="dark"] .pulse-stat,
        body[data-theme="dark"] .preview-item,
        body[data-theme="dark"] .preview-shell,
        body[data-theme="dark"] .preview-search,
        body[data-theme="dark"] .suggest-panel{
            background:var(--surface-strong)!important;
            color:var(--ink)!important;
            border-color:rgba(59,130,246,.18)!important;
            box-shadow:var(--shadow-soft)!important;
        }
        body[data-theme="dark"] .section-head h2,
        body[data-theme="dark"] .pulse-panel h3,
        body[data-theme="dark"] .fw-bold,
        body[data-theme="dark"] .fw-semibold,
        body[data-theme="dark"] .form-label,
        body[data-theme="dark"] .preview-item a,
        body[data-theme="dark"] .preview-item .fw-semibold{
            color:var(--ink)!important;
        }
        body[data-theme="dark"] .suggest-item:hover{
            background:var(--soft);
        }
        body[data-theme="dark"] .form-control::placeholder,
        body[data-theme="dark"] input::placeholder{
            color:#8da0be;
        }
    </style>
</head>
<body>
    <div class="nav-wrap">
        <nav class="navbar navbar-expand-lg py-3">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-3 fw-bold m-0" href="{{ route('website.home') }}" style="color:var(--ink);">
                    <span class="brand-mark"><i class="bi bi-code-slash"></i></span>
                    <span>
                        <span style="display:block;font-size:1.05rem;line-height:1.1;">CodeXpress</span>
                        <span style="display:block;font-size:.72rem;letter-spacing:.11em;text-transform:uppercase;color:var(--indigo);">Institute</span>
                    </span>
                </a>
                <button class="navbar-toggler nav-toggle d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#websiteNav" aria-controls="websiteNav" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="bi bi-list fs-4"></i>
                </button>
                <div class="collapse navbar-collapse" id="websiteNav">
                    <div class="navbar-nav ms-auto align-items-lg-center gap-lg-1">
                        <a href="#courses" class="nav-link">Courses</a>
                        <a href="#features" class="nav-link">Features</a>
                        <a href="#process" class="nav-link">How It Works</a>
                        <a href="#register" class="nav-link">Register</a>
                        <button type="button" class="theme-toggle ms-lg-2 mt-2 mt-lg-0" id="themeToggle" aria-label="Toggle website theme">
                            <i class="bi bi-moon-stars-fill"></i>
                        </button>
                        <a href="#register" class="btn btn-sm btn-brand ms-lg-2 mt-2 mt-lg-0">Apply Now</a>
                    </div>
                </div>
            </div>
        </nav>
    </div>

    <section class="hero">
        <div class="container">
            @if(session('success'))
                <div class="alert alert-success border-0 rounded-4 shadow-sm fade-up">{{ session('success') }}</div>
            @endif

            <div class="hero-grid">
                <div class="hero-copy fade-up">
                    <h1>Shape your future with <span class="hero-accent">CodeXpress Institute</span>.</h1>
                    <p>CodeXpress Institute combines structured teaching, subject-based course delivery, assignments, MCQ exam practice, progress tracking, and portal access into one serious learning experience.</p>
                    <div class="hero-actions">
                        <a href="#register" class="btn btn-dark-soft">Register Now <i class="bi bi-chevron-right ms-2"></i></a>
                        <a href="#courses" class="btn btn-ghost"><i class="bi bi-search me-2"></i>View Courses</a>
                    </div>
                    <div class="student-strip">
                        <div class="student-avatars" aria-hidden="true">
                            <div class="student-avatar"><img src="https://images.unsplash.com/photo-1544717305-2782549b5136?auto=format&fit=crop&w=220&q=80" alt="Student"></div>
                            <div class="student-avatar"><img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=220&q=80" alt="Student"></div>
                            <div class="student-avatar"><img src="https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=220&q=80" alt="Student"></div>
                        </div>
                        <div class="student-strip-copy">
                            <strong>Live student journeys</strong>
                            <span>Practical classes, guided assessments, and a structured digital learning flow.</span>
                        </div>
                    </div>
                    <div class="hero-mini">
                        <div class="mini-tile"><i class="bi bi-folder-check"></i><span>Assignment Flow</span></div>
                        <div class="mini-tile"><i class="bi bi-patch-question"></i><span>MCQ Exam System</span></div>
                        <div class="mini-tile"><i class="bi bi-people"></i><span>Dual Portals</span></div>
                    </div>
                </div>

                <div class="pulse-panel fade-up d2">
                    <div class="pulse-kicker">Institute Pulse</div>
                    <h3>Search institute subjects.</h3>
                    <p>Browse live subjects from the academic database and jump straight to registration with the subject you want.</p>

                    <div class="pulse-stats">
                        <div class="pulse-stat">
                            <strong>{{ $subjects->count() }}+</strong>
                            <span>Subjects</span>
                        </div>
                        <div class="pulse-stat">
                            <strong>CBT</strong>
                            <span>Quiz</span>
                        </div>
                        <div class="pulse-stat">
                            <strong>Live</strong>
                            <span>Hub</span>
                        </div>
                    </div>

                    <div class="preview-shell">
                        <div class="preview-search">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-search text-muted"></i>
                                <input type="text" id="heroSubjectSearch" placeholder="Search active subjects...">
                            </div>
                        </div>
                        <div id="heroSubjectResults">
                            @foreach($subjects->take(3) as $subject)
                                <div class="preview-item">
                                    <div>
                                        <div class="fw-semibold">{{ $subject->subject_name }}</div>
                                        <small>{{ $subject->description ?: 'Core system workflow' }}</small>
                                    </div>
                                    <a href="#register" class="hero-enroll-link" data-subject-id="{{ $subject->id }}">Enroll <i class="bi bi-chevron-right"></i></a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section" id="features">
        <div class="container">
            <div class="feature-row">
                <div class="feature-card fade-up d1">
                    <i class="bi bi-journal-code"></i>
                    <div class="fw-bold mb-1">Subject-Focused Teaching</div>
                    <div class="text-muted small">Real subjects pulled from the live academic database.</div>
                </div>
                <div class="feature-card fade-up d2">
                    <i class="bi bi-patch-question-fill"></i>
                    <div class="fw-bold mb-1">Exam-Style MCQs</div>
                    <div class="text-muted small">Better test flow with timing, results, and question navigation.</div>
                </div>
                <div class="feature-card fade-up d3">
                    <i class="bi bi-kanban-fill"></i>
                    <div class="fw-bold mb-1">Assignment Tracking</div>
                    <div class="text-muted small">Post, submit, grade, and review in one structured process.</div>
                </div>
                <div class="feature-card fade-up d4">
                    <i class="bi bi-person-check-fill"></i>
                    <div class="fw-bold mb-1">Approval Workflow</div>
                    <div class="text-muted small">Website registrations flow into admin review before account creation.</div>
                </div>
            </div>
        </div>
    </section>

    <section class="section" id="courses">
        <div class="container">
            <div class="course-shell fade-up d2">
                <div class="section-head mb-4">
                    <div>
                        <div class="section-kicker">Courses</div>
                        <h2>Current learning tracks at CodeXpress.</h2>
                    </div>
                </div>
                <div class="course-grid collapsed" id="courseGrid">
                    @foreach($subjects as $subject)
                        <article class="course-card">
                            <span class="course-code">{{ $subject->subject_code }}</span>
                            <h3>{{ $subject->subject_name }}</h3>
                            <p>{{ $subject->description ?: 'Hands-on lessons, guided practice, structured assignments, and digital progress tracking for this subject.' }}</p>
                            <div class="course-meta">
                                <span>{{ $subject->category ?: 'General Stream' }}</span>
                                <i class="bi bi-arrow-up-right-circle fs-5" style="color:var(--orange);"></i>
                            </div>
                        </article>
                    @endforeach
                </div>
                @if($subjects->count() > 8)
                    <div class="view-more-wrap">
                        <button type="button" class="btn btn-soft" id="toggleCoursesBtn">View More Subjects</button>
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section class="section" id="process">
        <div class="container">
            <div class="process-shell fade-up d1">
                <div class="section-head mb-4">
                    <div>
                        <div class="section-kicker">How It Works</div>
                        <h2>From website application to student portal access.</h2>
                        <p>The full intake flow is already connected to the system and admin approval process.</p>
                    </div>
                </div>
                <div class="process-grid">
                    <div class="process-card">
                        <div class="step-badge">1</div>
                        <div class="fw-bold mb-2">Apply Online</div>
                        <div class="text-muted small" style="line-height:1.8;">Students fill the website registration form with personal details and preferred subjects.</div>
                    </div>
                    <div class="process-card">
                        <div class="step-badge">2</div>
                        <div class="fw-bold mb-2">Pending Review</div>
                        <div class="text-muted small" style="line-height:1.8;">The request goes into the pending student queue inside the admin dashboard.</div>
                    </div>
                    <div class="process-card">
                        <div class="step-badge">3</div>
                        <div class="fw-bold mb-2">Admin Approval</div>
                        <div class="text-muted small" style="line-height:1.8;">Admin can approve or dismiss, assign a class, and finalize the subject list.</div>
                    </div>
                    <div class="process-card">
                        <div class="step-badge">4</div>
                        <div class="fw-bold mb-2">Student Account Created</div>
                        <div class="text-muted small" style="line-height:1.8;">The system generates a registration number and creates the student portal login record.</div>
                    </div>
                    <div class="process-card">
                        <div class="step-badge">5</div>
                        <div class="fw-bold mb-2">Portal Learning Starts</div>
                        <div class="text-muted small" style="line-height:1.8;">Students can then use the portal for subjects, assignments, MCQs, marks, and notifications.</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section" id="register">
        <div class="container">
            <div class="register-shell-wrap fade-up d3">
                <div class="section-head mb-4">
                    <div>
                        <div class="section-kicker">Registration</div>
                        <h2>Apply to CodeXpress Institute.</h2>
                        <p>Submit your details and choose the subjects you want. The admin team will review and approve your request before account activation.</p>
                    </div>
                </div>
                <div class="register-grid">
                    <div class="register-form">
                        <form action="{{ route('website.register') }}" method="POST" id="websiteRegistrationForm">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <label class="form-label">Full Name</label>
                                    <input type="text" name="full_name" class="form-control" value="{{ old('full_name') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Date of Birth</label>
                                    <input type="date" name="dob" class="form-control" value="{{ old('dob') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Gender</label>
                                    <select name="gender" class="form-select" required>
                                        <option value="">Select gender...</option>
                                        <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                                        <option value="Other" {{ old('gender') === 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                                <div class="col-md-12 suggest-shell">
                                    <label class="form-label">Subjects You Want</label>
                                    <input type="text" id="subjectSearchInput" class="form-control" placeholder="Search subject by name or code">
                                    <div class="suggest-panel" id="subjectSuggestPanel"></div>
                                    <div id="subjectUnavailable" class="small text-danger mt-2" style="display:none;">Subject not available.</div>
                                </div>
                                <div class="col-md-12">
                                    <div id="selectedSubjectChips" class="chip-wrap"></div>
                                    @error('subject_ids')
                                        <div class="text-danger small mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            @if($errors->any())
                                <div class="alert alert-danger rounded-4 mt-3 mb-0">
                                    <ul class="mb-0 ps-3">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <button type="submit" class="btn btn-brand w-100 mt-4">Submit Registration Request</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="section pt-2">
        <div class="container">
            <div class="cta-panel fade-up d4">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <div class="section-kicker">Start Here</div>
                        <h2 class="mt-2 mb-2">Ready to join CodeXpress Institute?</h2>
                        <p class="mb-0 text-muted" style="line-height:1.85;">Apply online, get reviewed by admin, and move into a structured digital learning system built for real teaching and real progress.</p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <a href="#register" class="btn btn-brand">Apply as Student</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <div class="container d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <div class="fw-bold text-dark">CodeXpress Institute</div>
                <div class="small">Website registration, course discovery, teacher workflows, student portal, assignments, MCQs, and marks in one system.</div>
            </div>
            <div class="d-flex gap-3">
                <a href="#courses" class="text-decoration-none small" style="color:inherit;">Courses</a>
                <a href="#features" class="text-decoration-none small" style="color:inherit;">Features</a>
                <a href="#process" class="text-decoration-none small" style="color:inherit;">Process</a>
                <a href="#register" class="text-decoration-none small" style="color:inherit;">Register</a>
            </div>
        </div>
    </footer>

    <script>
    const allWebsiteSubjects = @json($subjectDirectory);
    const selectedSubjectIds = new Set(@json(old('subject_ids', [])));
    const form = document.getElementById('websiteRegistrationForm');
    const input = document.getElementById('subjectSearchInput');
    const panel = document.getElementById('subjectSuggestPanel');
    const unavailable = document.getElementById('subjectUnavailable');
    const chipContainer = document.getElementById('selectedSubjectChips');
    const heroSearchInput = document.getElementById('heroSubjectSearch');
    const heroResults = document.getElementById('heroSubjectResults');
    const courseGrid = document.getElementById('courseGrid');
    const toggleCoursesBtn = document.getElementById('toggleCoursesBtn');
    const themeToggle = document.getElementById('themeToggle');
    const storedTheme = localStorage.getItem('codexpress-theme') || 'light';

    function applyTheme(theme) {
        document.body.dataset.theme = theme;
        themeToggle.innerHTML = theme === 'dark'
            ? '<i class="bi bi-sun-fill"></i>'
            : '<i class="bi bi-moon-stars-fill"></i>';
        localStorage.setItem('codexpress-theme', theme);
    }

    applyTheme(storedTheme);

    themeToggle.addEventListener('click', () => {
        const nextTheme = document.body.dataset.theme === 'dark' ? 'light' : 'dark';
        applyTheme(nextTheme);
    });

    function renderSelectedSubjects() {
        chipContainer.innerHTML = '';
        form.querySelectorAll('input[name="subject_ids[]"]').forEach((inputEl) => inputEl.remove());

        selectedSubjectIds.forEach((subjectId) => {
            const subject = allWebsiteSubjects.find((item) => item.id === subjectId);
            if (!subject) return;

            const chip = document.createElement('span');
            chip.className = 'subject-chip';
            chip.innerHTML = `${subject.name} <button type="button" data-id="${subject.id}" aria-label="Remove subject"><i class="bi bi-x-circle"></i></button>`;
            chip.querySelector('button').addEventListener('click', () => {
                selectedSubjectIds.delete(subject.id);
                renderSelectedSubjects();
            });
            chipContainer.appendChild(chip);

            const hidden = document.createElement('input');
            hidden.type = 'hidden';
            hidden.name = 'subject_ids[]';
            hidden.value = subject.id;
            form.appendChild(hidden);
        });
    }

    function renderHeroSubjects(matches) {
        heroResults.innerHTML = matches.length
            ? matches.map((subject) => `
                <div class="preview-item">
                    <div>
                        <div class="fw-semibold">${subject.name}</div>
                        <small>${subject.description || 'Core system workflow'}</small>
                    </div>
                    <a href="#register" class="hero-enroll-link" data-subject-id="${subject.id}">Enroll <i class="bi bi-chevron-right"></i></a>
                </div>
            `).join('')
            : `<div class="preview-item"><div><div class="fw-semibold">No subject found</div><small>Try another subject name or code.</small></div></div>`;

        heroResults.querySelectorAll('.hero-enroll-link').forEach((link) => {
            link.addEventListener('click', () => {
                selectedSubjectIds.add(parseInt(link.dataset.subjectId, 10));
                renderSelectedSubjects();
            });
        });
    }

    function renderSuggestions(matches, query) {
        unavailable.style.display = query && matches.length === 0 ? 'block' : 'none';
        panel.innerHTML = matches.map((subject) => `
            <div class="suggest-item" data-id="${subject.id}">
                <div class="fw-semibold">${subject.name}</div>
                <div class="text-muted small">${subject.code}</div>
            </div>
        `).join('');
        panel.style.display = matches.length ? 'block' : 'none';

        panel.querySelectorAll('.suggest-item').forEach((item) => {
            item.addEventListener('click', () => {
                selectedSubjectIds.add(parseInt(item.dataset.id, 10));
                input.value = '';
                panel.style.display = 'none';
                unavailable.style.display = 'none';
                renderSelectedSubjects();
            });
        });
    }

    heroSearchInput?.addEventListener('input', () => {
        const query = heroSearchInput.value.trim().toLowerCase();
        const matches = allWebsiteSubjects
            .filter((subject) =>
                subject.name.toLowerCase().includes(query) || subject.code.toLowerCase().includes(query)
            )
            .slice(0, 5)
            .map((subject) => ({
                ...subject,
                description: subject.description || ''
            }));
        renderHeroSubjects(query ? matches : allWebsiteSubjects.slice(0, 5));
    });

    if (toggleCoursesBtn && courseGrid) {
        toggleCoursesBtn.addEventListener('click', () => {
            const expanded = !courseGrid.classList.contains('collapsed');
            courseGrid.classList.toggle('collapsed', expanded);
            toggleCoursesBtn.textContent = expanded ? 'View More Subjects' : 'Show Less';
        });
    }

    input.addEventListener('input', () => {
        const query = input.value.trim().toLowerCase();
        if (!query) {
            panel.style.display = 'none';
            unavailable.style.display = 'none';
            return;
        }

        const matches = allWebsiteSubjects.filter((subject) =>
            subject.name.toLowerCase().includes(query) || subject.code.toLowerCase().includes(query)
        );

        renderSuggestions(matches, query);
    });

    document.addEventListener('click', (event) => {
        if (!event.target.closest('.suggest-shell')) {
            panel.style.display = 'none';
        }
    });

    renderSelectedSubjects();
    renderHeroSubjects(allWebsiteSubjects.slice(0, 5));
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
