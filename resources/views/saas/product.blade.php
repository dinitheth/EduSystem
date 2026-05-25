<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduSystem Cloud - Institute Management Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --ink: #111827;
            --muted: #64748b;
            --line: #dbeafe;
            --blue: #2563eb;
            --cyan: #06b6d4;
            --green: #10b981;
            --orange: #f59e0b;
            --paper: #ffffff;
            --soft: #f4f9ff;
        }
        * { box-sizing: border-box; }
        body { margin:0; font-family: Inter, "Segoe UI", Arial, sans-serif; color:var(--ink); background:var(--soft); }
        a { color:inherit; text-decoration:none; }
        .wrap { width:min(1180px, calc(100% - 32px)); margin:0 auto; }
        .nav { position:sticky; top:0; z-index:20; background:rgba(255,255,255,.94); backdrop-filter:blur(18px); border-bottom:1px solid var(--line); }
        .nav-inner { min-height:76px; display:flex; align-items:center; justify-content:space-between; gap:20px; }
        .brand { display:flex; align-items:center; gap:12px; font-weight:950; }
        .mark { width:46px; height:46px; border-radius:14px; display:grid; place-items:center; color:#fff; background:linear-gradient(135deg,var(--blue),var(--cyan)); box-shadow:0 14px 34px rgba(37,99,235,.28); }
        .brand small { display:block; color:var(--blue); letter-spacing:.12em; font-size:.68rem; margin-top:1px; }
        .nav-links { display:flex; align-items:center; gap:26px; color:#41516a; font-weight:800; font-size:.93rem; }
        .btn { display:inline-flex; align-items:center; justify-content:center; gap:8px; border:1px solid var(--line); border-radius:14px; padding:13px 18px; font-weight:900; transition:.18s ease; cursor:pointer; background:#fff; }
        .btn:hover { transform:translateY(-2px); box-shadow:0 12px 28px rgba(16,32,51,.12); }
        .btn-dark { background:#0f172a; color:#fff; border-color:#0f172a; }
        .btn-blue { background:linear-gradient(135deg,var(--blue),var(--cyan)); color:#fff; border:0; }
        .hero {
            background:
                radial-gradient(circle at 78% 14%, rgba(6,182,212,.18), transparent 29%),
                linear-gradient(90deg, rgba(37,99,235,.055) 1px, transparent 1px),
                linear-gradient(rgba(37,99,235,.055) 1px, transparent 1px),
                linear-gradient(180deg,#fff 0%,#eef7ff 100%);
            background-size:auto,42px 42px,42px 42px,auto;
            border-bottom:1px solid var(--line);
        }
        .hero-grid { min-height:690px; display:grid; grid-template-columns:1.02fr .98fr; gap:42px; align-items:center; padding:68px 0; }
        .eyebrow { display:inline-flex; align-items:center; gap:8px; border:1px solid #c8d9ff; background:#eef4ff; color:var(--blue); padding:9px 13px; border-radius:999px; font-weight:950; font-size:.78rem; letter-spacing:.06em; }
        h1 { font-size:clamp(3rem, 5.8vw, 5.6rem); line-height:.95; margin:24px 0 22px; letter-spacing:0; }
        .grad { background:linear-gradient(135deg,#4f46e5 0%,#0ea5e9 52%,#10b981 100%); -webkit-background-clip:text; color:transparent; }
        .lead { font-size:1.18rem; line-height:1.75; color:var(--muted); max-width:700px; }
        .hero-actions { display:flex; gap:14px; flex-wrap:wrap; margin-top:28px; }
        .proof-row { display:grid; grid-template-columns:repeat(3, minmax(0,1fr)); gap:12px; margin-top:34px; max-width:700px; }
        .proof { background:rgba(255,255,255,.82); border:1px solid var(--line); border-radius:18px; padding:16px; display:flex; gap:12px; align-items:center; font-weight:850; }
        .proof i { color:var(--blue); font-size:1.2rem; }
        .product-card { background:rgba(255,255,255,.88); border:1px solid var(--line); border-radius:30px; padding:24px; box-shadow:0 28px 80px rgba(16,32,51,.13); }
        .screen { background:#0f172a; color:#dbeafe; border-radius:22px; overflow:hidden; border:1px solid #1e3a5f; }
        .screen-top { height:48px; display:flex; align-items:center; gap:7px; padding:0 16px; background:#111c31; }
        .dot { width:10px; height:10px; border-radius:50%; background:#ef4444; }
        .dot:nth-child(2){ background:#f59e0b; } .dot:nth-child(3){ background:#22c55e; }
        .screen-body { padding:20px; }
        .mini-grid { display:grid; grid-template-columns:repeat(2, minmax(0,1fr)); gap:12px; margin-bottom:18px; }
        .mini-stat { border:1px solid rgba(148,163,184,.25); border-radius:16px; padding:16px; background:rgba(15,23,42,.55); }
        .mini-stat strong { display:block; color:#fff; font-size:1.7rem; }
        .flow-list { display:grid; gap:10px; }
        .flow-item { display:flex; gap:12px; align-items:flex-start; padding:13px; border-radius:14px; background:#15233d; border:1px solid rgba(148,163,184,.18); }
        .flow-item i { color:#38bdf8; margin-top:2px; }
        section { padding:78px 0; }
        .section-head { display:flex; justify-content:space-between; align-items:end; gap:24px; margin-bottom:28px; }
        .kicker { color:var(--blue); text-transform:uppercase; letter-spacing:.12em; font-weight:950; font-size:.78rem; }
        h2 { margin:8px 0 0; font-size:clamp(2rem, 4vw, 3.15rem); line-height:1.04; letter-spacing:0; }
        .section-head p { color:var(--muted); line-height:1.7; max-width:560px; margin:0; }
        .feature-grid { display:grid; grid-template-columns:repeat(3, minmax(0,1fr)); gap:18px; }
        .feature { background:var(--paper); border:1px solid var(--line); border-radius:20px; padding:22px; box-shadow:0 16px 40px rgba(16,32,51,.06); }
        .feature i { width:42px; height:42px; display:grid; place-items:center; border-radius:13px; color:#fff; background:linear-gradient(135deg,var(--blue),var(--cyan)); margin-bottom:18px; }
        .feature h3 { margin:0 0 8px; font-size:1.08rem; }
        .feature p { margin:0; color:var(--muted); line-height:1.62; }
        .process { background:#fff; border-top:1px solid var(--line); border-bottom:1px solid var(--line); }
        .steps { display:grid; grid-template-columns:repeat(4, minmax(0,1fr)); gap:16px; }
        .step { background:#f8fbff; border:1px solid var(--line); border-radius:22px; padding:22px; }
        .step-num { width:34px; height:34px; border-radius:12px; display:grid; place-items:center; color:#fff; background:linear-gradient(135deg,var(--blue),var(--cyan)); font-weight:950; margin-bottom:16px; }
        .custom-panel { display:grid; grid-template-columns:.9fr 1.1fr; gap:22px; align-items:stretch; }
        .palette { background:#0f172a; color:#fff; border-radius:28px; padding:30px; box-shadow:0 24px 70px rgba(15,23,42,.22); }
        .settings-list { display:grid; gap:12px; }
        .setting { background:#fff; border:1px solid var(--line); border-radius:18px; padding:18px; display:flex; gap:12px; align-items:center; }
        .setting i { color:var(--green); font-size:1.2rem; }
        .pricing { background:#fff; border-top:1px solid var(--line); border-bottom:1px solid var(--line); }
        .plans { display:grid; grid-template-columns:repeat(3, minmax(0,1fr)); gap:18px; }
        .plan { position:relative; border:1px solid var(--line); border-radius:24px; padding:26px; background:#fff; box-shadow:0 18px 48px rgba(16,32,51,.07); }
        .plan.hot { border:2px solid var(--blue); transform:translateY(-8px); box-shadow:0 28px 80px rgba(37,99,235,.18); }
        .tag { position:absolute; top:18px; right:18px; background:#dcfce7; color:#047857; border-radius:999px; padding:6px 10px; font-size:.74rem; font-weight:950; }
        .price { font-size:2.25rem; font-weight:950; margin:16px 0 2px; }
        .period { color:var(--muted); font-weight:800; }
        .plan ul { padding:0; margin:22px 0; list-style:none; display:grid; gap:11px; }
        .plan li { display:flex; gap:10px; color:#42526a; line-height:1.45; }
        .plan li i { color:var(--green); }
        .cta { background:linear-gradient(135deg,#102033,#143861 55%,#0e7490); color:#fff; }
        .cta-box { display:flex; align-items:center; justify-content:space-between; gap:24px; }
        .cta p { color:#c8d7e8; line-height:1.7; max-width:680px; }
        footer { padding:34px 0; color:#607087; background:#fff; }
        @media (max-width: 980px) {
            .nav-links { display:none; }
            .hero-grid,.custom-panel,.plans,.steps { grid-template-columns:1fr; }
            .hero-grid { min-height:auto; padding:48px 0; }
            .proof-row,.feature-grid { grid-template-columns:1fr; }
            .section-head,.cta-box { display:block; }
            .product-card { padding:14px; }
            .plan.hot { transform:none; }
        }
    </style>
</head>
<body>
    <nav class="nav">
        <div class="wrap nav-inner">
            <a href="{{ route('saas.product') }}" class="brand">
                <span class="mark"><i class="bi bi-grid-1x2-fill"></i></span>
                <span>EduSystem Cloud<small>INSTITUTE MANAGEMENT PLATFORM</small></span>
            </a>
            <div class="nav-links">
                <a href="#features">Features</a>
                <a href="#workflow">How It Works</a>
                <a href="#customize">Customization</a>
                <a href="#pricing">Pricing</a>
                <a href="#pricing" class="btn btn-blue">View Plans <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </nav>

    <header class="hero">
        <div class="wrap hero-grid">
            <div>
                <span class="eyebrow"><i class="bi bi-stars"></i> Built for modern education teams</span>
                <h1>Run your institute with a smarter <span class="grad">academic platform.</span></h1>
                <p class="lead">EduSystem Cloud helps schools, academies, tuition centers, and training institutes manage admissions, students, teachers, classes, assignments, MCQ exams, marks, notifications, and reporting from one connected system.</p>
                <div class="hero-actions">
                    <a class="btn btn-dark" href="#pricing">Compare Plans <i class="bi bi-chevron-right"></i></a>
                    <a class="btn" href="#features">Explore Features <i class="bi bi-grid"></i></a>
                </div>
                <div class="proof-row">
                    <div class="proof"><i class="bi bi-person-check"></i> Faster admissions</div>
                    <div class="proof"><i class="bi bi-patch-question"></i> Online exams</div>
                    <div class="proof"><i class="bi bi-bar-chart"></i> Clear progress</div>
                </div>
            </div>
            <div class="product-card">
                <div class="screen">
                    <div class="screen-top"><span class="dot"></span><span class="dot"></span><span class="dot"></span></div>
                    <div class="screen-body">
                        <div class="mini-grid">
                            <div class="mini-stat"><strong>Admin</strong><span>Operations</span></div>
                            <div class="mini-stat"><strong>Teacher</strong><span>Teaching flow</span></div>
                            <div class="mini-stat"><strong>Student</strong><span>Learning portal</span></div>
                            <div class="mini-stat"><strong>Reports</strong><span>Marks and exports</span></div>
                        </div>
                        <div class="flow-list">
                            <div class="flow-item"><i class="bi bi-check-circle-fill"></i><span>Students apply online and enter a pending approval queue.</span></div>
                            <div class="flow-item"><i class="bi bi-check-circle-fill"></i><span>Admins approve students, assign class, subjects, and portal access.</span></div>
                            <div class="flow-item"><i class="bi bi-check-circle-fill"></i><span>Teachers publish lessons, assignments, MCQs, grades, and feedback.</span></div>
                            <div class="flow-item"><i class="bi bi-check-circle-fill"></i><span>Students track work, results, notifications, and academic progress.</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <section id="features">
        <div class="wrap">
            <div class="section-head">
                <div><span class="kicker">Platform features</span><h2>Everything an institute needs to operate clearly.</h2></div>
                <p>The platform brings academic administration, teacher workflows, student self-service, online assessments, and performance visibility into one structured product.</p>
            </div>
            <div class="feature-grid">
                @foreach($features as $index => $feature)
                    @php($icons = ['bi-person-lines-fill','bi-speedometer2','bi-diagram-3','bi-file-earmark-check','bi-patch-question','bi-award','bi-bell','bi-download','bi-sliders2-vertical'])
                    <div class="feature">
                        <i class="bi {{ $icons[$index] ?? 'bi-check2-circle' }}"></i>
                        <h3>{{ $feature }}</h3>
                        <p>Designed to reduce manual work, improve visibility, and keep students, teachers, and admins aligned.</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="process" id="workflow">
        <div class="wrap">
            <div class="section-head">
                <div><span class="kicker">How it works</span><h2>A practical flow from admission to results.</h2></div>
                <p>EduSystem Cloud follows the real daily rhythm of an education organization: enroll students, assign learning, assess performance, and communicate results.</p>
            </div>
            <div class="steps">
                <div class="step"><div class="step-num">1</div><h3>Enroll</h3><p>Collect student details, requested subjects, and contact information through a clean registration workflow.</p></div>
                <div class="step"><div class="step-num">2</div><h3>Organize</h3><p>Assign classes, subjects, teachers, student accounts, and course content from the admin side.</p></div>
                <div class="step"><div class="step-num">3</div><h3>Teach</h3><p>Teachers publish assignments, upload content, schedule MCQs, and review submissions.</p></div>
                <div class="step"><div class="step-num">4</div><h3>Measure</h3><p>Marks, grades, MCQ results, assignment scores, notifications, and exports keep progress visible.</p></div>
            </div>
        </div>
    </section>

    <section id="customize">
        <div class="wrap custom-panel">
            <div class="palette">
                <span class="kicker" style="color:#67e8f9;">Configurable system</span>
                <h2>Adapt the platform to each organization.</h2>
                <p style="color:#cbd5e1;line-height:1.75;">Each institute can configure branding, dashboard labels, enabled modules, visible widgets, academic workflows, portals, and operational controls.</p>
            </div>
            <div class="settings-list">
                <div class="setting"><i class="bi bi-palette"></i><span><strong>Brand setup:</strong> organization name, logo, colors, and portal identity.</span></div>
                <div class="setting"><i class="bi bi-layout-sidebar"></i><span><strong>Portal setup:</strong> control what students, teachers, and admins see in their dashboards.</span></div>
                <div class="setting"><i class="bi bi-toggles2"></i><span><strong>Module setup:</strong> enable or disable assignments, MCQs, marks, notifications, exports, and content tools.</span></div>
                <div class="setting"><i class="bi bi-gear-wide-connected"></i><span><strong>Workflow setup:</strong> configure approvals, auto-numbering, quiz timing, grading sync, and teacher/student access rules.</span></div>
            </div>
        </div>
    </section>

    <section class="pricing" id="pricing">
        <div class="wrap">
            <div class="section-head">
                <div><span class="kicker">Pricing in LKR</span><h2>Choose a plan that matches your institute size.</h2></div>
                <p>Start with a trial, move into a complete operating plan, or choose the Pro plan for advanced configuration and support.</p>
            </div>
            <div class="plans">
                @foreach($plans as $plan)
                    <div class="plan {{ $plan['highlight'] ? 'hot' : '' }}">
                        @if($plan['highlight'])
                            <span class="tag">Popular</span>
                        @endif
                        <h3>{{ $plan['name'] }}</h3>
                        <div class="price">{{ $plan['price'] }}</div>
                        <div class="period">{{ $plan['period'] }}</div>
                        <p style="color:var(--muted);line-height:1.6;">{{ $plan['description'] }}</p>
                        <ul>
                            @foreach($plan['features'] as $feature)
                                <li><i class="bi bi-check-circle-fill"></i><span>{{ $feature }}</span></li>
                            @endforeach
                        </ul>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="cta">
        <div class="wrap cta-box">
            <div>
                <span class="kicker" style="color:#67e8f9;">Built for serious education teams</span>
                <h2>Bring admissions, teaching, exams, and progress tracking into one platform.</h2>
                <p>EduSystem Cloud gives institutes a clean way to manage day-to-day academic operations while keeping every role connected through dedicated portals.</p>
            </div>
            <a class="btn btn-blue" href="#pricing">Review Plans</a>
        </div>
    </section>

    <footer>
        <div class="wrap" style="display:flex;justify-content:space-between;gap:20px;flex-wrap:wrap;">
            <span>EduSystem Cloud</span>
            <span>Institute management platform for modern education teams</span>
        </div>
    </footer>
</body>
</html>
