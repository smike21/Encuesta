<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Página de Encuestas')</title>
    <style>
        :root { --ink:#102a44; --muted:#51595f; --cream:#c2c9d1; --paper:#adb7bf; --accent:#1e5bb0; --accent-dark:#163f7a; --line:#a8b2bb; --shadow:0 18px 42px rgba(35,45,55,.08); }
        * { box-sizing:border-box; } body { margin:0; min-height:100vh; font-family:Inter,Segoe UI,system-ui,sans-serif; color:var(--ink); background:radial-gradient(circle at top left,rgba(150,160,170,.25) 0,transparent 31rem),var(--cream); line-height:1.55; }
        a { color:var(--accent); text-decoration:none; font-weight:600; } a:hover { color:var(--accent-dark); }
        .navbar { background:rgba(198,206,214,.98); backdrop-filter:blur(12px); border-bottom:1px solid var(--line); position:sticky; top:0; z-index:10; }
        .container { width:min(1120px,calc(100% - 2.5rem)); margin:auto; } .navbar .container { min-height:74px; display:flex; align-items:center; justify-content:space-between; gap:1rem; }
        .navbar-brand { color:var(--ink)!important; font-size:1.05rem; font-weight:800; letter-spacing:-.02em; display:inline-flex; align-items:center; gap:.75rem; }
        .navbar-brand img.navbar-logo { height:120px; width:auto; display:block; }
        main.container { padding:4.25rem 0 5rem; } h1,h2,h3 { letter-spacing:-.035em; line-height:1.08; } h1 { font-size:clamp(2.2rem,5vw,3.75rem); margin:0 0 .65rem; } h2 { margin-top:0; } p { color:var(--muted); } .mb-5 { margin-bottom:3rem!important; } .mb-4 { margin-bottom:1.5rem!important; } .mb-3 { margin-bottom:1rem!important; } .mt-4 { margin-top:1.5rem!important; } .mt-3 { margin-top:1rem!important; }
        .row { display:grid; grid-template-columns:repeat(12,1fr); gap:1.35rem; } .col-md-6 { grid-column:span 6; } .col-lg-4 { grid-column:span 4; } .col-lg-8 { grid-column:3 / span 8; } .col-md-4 { grid-column:span 4; } .col-md-5 { grid-column:4 / span 6; }
        .card { overflow:hidden; border:1px solid rgba(85,90,99,.16); border-radius:22px; background:var(--paper); box-shadow:var(--shadow); } .card-body { padding:1.55rem; } .card-footer,.card-header { padding:1rem 1.55rem; background:#c9cfd6; border:0; } .card-header { font-weight:750; } .h-100 { height:100%; } .h5 { font-size:1.22rem; } .h4 { font-size:1.45rem; } .h3 { font-size:1.8rem; }
        .btn { display:inline-flex; align-items:center; justify-content:center; border:1px solid transparent; border-radius:999px; padding:.72rem 1.15rem; background:var(--accent); color:#fff!important; font:inherit; font-weight:750; cursor:pointer; box-shadow:0 9px 18px rgba(35,45,55,.12); transition:transform .18s,background .18s; } .btn:hover { background:var(--accent-dark); transform:translateY(-2px); } .btn-outline-primary { color:var(--accent)!important; background:#e7eaf0; border-color:#cbd2db; box-shadow:none; } .btn-outline-warning { color:#4a5f83!important; background:#edf0f5; border-color:#d3d8e1; box-shadow:none; } .btn-outline-danger { color:#8b1f3d!important; background:#f9eef4; border-color:#e4d0db; box-shadow:none; } .btn-sm { padding:.48rem .8rem; font-size:.84rem; } .w-100 { width:100%; }
        .d-flex { display:flex; } .justify-content-between { justify-content:space-between; } .align-items-center { align-items:center; } .gap-2 { gap:.55rem; } .gap-4 { gap:1.35rem; } .flex-wrap { flex-wrap:wrap; } .d-inline { display:inline; } .d-grid { display:grid; } .float-end { float:right; }
        .form-label { display:block; margin:.25rem 0 .45rem; font-weight:750; } .form-control,.form-select { width:100%; padding:.78rem .9rem; border:1px solid #a8afb7; border-radius:12px; background:#cdd4db; color:var(--ink); font:inherit; outline:none; } .form-control:focus,.form-select:focus { border-color:var(--accent); box-shadow:0 0 0 4px rgba(30,91,176,.13); } textarea.form-control { resize:vertical; min-height:90px; } .form-check { margin:.55rem 0; } .form-check-input { accent-color:var(--accent); margin-right:.35rem; } .btn-close { border:0; background:transparent; font-size:1rem; cursor:pointer; } .btn-close:after { content:'×'; }
        .badge { display:inline-block; border-radius:999px; padding:.32rem .65rem; font-size:.73rem; font-weight:800; } .text-bg-success { color:#125f3d; background:#dcedf3; } .text-bg-secondary { color:#475d7e; background:#e7eef7; } .alert { margin-bottom:1.25rem; padding:1rem 1.2rem; border-radius:14px; background:#f0f6ff; color:#1e3f72; border:1px solid #c5d8f2; } .alert-success { background:#e8f2ff; color:#1b4d88; border-color:#b8d4f2; } .alert-warning { background:#eaf3ff; color:#3d5f8c; }
        .border { border:1px solid var(--line); } .rounded { border-radius:16px; } .p-3 { padding:1rem; } .p-4 { padding:1.55rem; } .text-muted { color:var(--muted); } .text-danger { color:#8b1f3d; } .small { font-size:.86rem; } .list-group { margin:0; padding:0; list-style:none; } .list-group-item { padding:.9rem 1.2rem; border-top:1px solid var(--line); } .list-group-item:first-child { border-top:0; }
        .link-button { border:0; background:none; color:var(--accent); font:inherit; font-weight:700; cursor:pointer; margin-left:1rem; }.results-actions{display:flex;justify-content:space-between;gap:1rem;align-items:center;margin-bottom:2.4rem}.results-heading{margin-bottom:2.5rem}.eyebrow{display:block;color:var(--accent);font-weight:800;font-size:.75rem;letter-spacing:.12em;text-transform:uppercase;margin-bottom:.45rem}.summary-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:1rem}.summary-card{padding:1.35rem;border-radius:18px;background:var(--paper);border:1px solid var(--line);box-shadow:var(--shadow)}.summary-card strong{display:block;color:var(--accent);font-size:2.25rem;line-height:1}.summary-card span{color:var(--muted);font-weight:650}.section-title{margin-bottom:1.2rem}.section-title h2{font-size:2rem;margin:.1rem 0}.statistics-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:1rem}.statistic-card h3{font-size:1.12rem;margin:.55rem 0 1.2rem}.question-kind{display:inline-block;padding:.25rem .55rem;border-radius:999px;color:#1e3f72;background:#dce9ff;font-size:.72rem;font-weight:800}.bar-chart{display:grid;gap:.7rem}.bar-row{display:grid;grid-template-columns:minmax(75px,1fr) 3fr 28px;gap:.65rem;align-items:center;font-size:.88rem}.bar-label{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.bar-track{height:.75rem;border-radius:999px;background:#dbe9ff;overflow:hidden}.bar-fill{display:block;height:100%;border-radius:999px;background:linear-gradient(90deg,#3b82f6,#1e5bb0)}.open-answer-stat{display:flex;align-items:baseline;gap:.55rem;padding:.9rem;border-radius:14px;background:#e8f2ff}.open-answer-stat strong{font-size:2rem;color:var(--accent)}.time-row{display:grid;gap:.35rem}.time-row strong{color:var(--ink)}.account-list{display:grid;gap:1rem}.account-card{display:grid;grid-template-columns:1fr auto}.account-card .card-footer{display:flex;align-items:center}.account-title{display:flex;justify-content:space-between;gap:1rem;align-items:flex-start}.account-title h2{margin-bottom:.25rem}.account-title p{margin:0}.form-shell{max-width:760px;margin:auto}.permission-list{display:grid;gap:.65rem}.permission-item{display:flex;gap:.8rem;align-items:flex-start;padding:1rem;border:1px solid var(--line);border-radius:14px;cursor:pointer}.permission-item:hover{background:#fff8f2}.permission-item input{margin-top:.25rem;accent-color:var(--accent)}.permission-item strong,.permission-item small{display:block}.permission-item small{color:var(--muted);margin-top:.15rem}@media(max-width:760px){ .container{width:min(100% - 1.5rem,1120px)} main.container{padding:2.5rem 0 4rem}.row{display:block}.row>[class*="col-"]{margin-bottom:1rem}.col-lg-8,.col-md-5{width:100%}.navbar .container{min-height:64px} h1{font-size:2.35rem}.d-flex.justify-content-between{align-items:flex-start}.statistics-grid,.summary-grid{grid-template-columns:1fr}.results-actions{align-items:flex-start;flex-direction:column}.bar-row{grid-template-columns:85px 1fr 28px}.account-card{display:block}.account-card .card-footer{margin-top:0} }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="navbar">
        <div class="container" style="display:flex;align-items:center;justify-content:space-between;gap:1rem;">
            <div class="d-flex align-items-center gap-4">
                <a class="navbar-brand" href="{{ route('home') }}">
                    <img src="{{ asset('images/probien-logo.png') }}" alt="Probien" class="navbar-logo">
                    Encuestas
                </a>
            </div>
            <div>
                @auth
                    @if(auth()->user()->is_admin)
                        <a href="{{ route('admin.dashboard') }}">Panel</a>
                        <a href="{{ route('admin.password.form') }}">Cambiar contraseña</a>
                    @else
                        <a href="{{ route('surveyor.dashboard') }}">Mis resultados</a>
                    @endif
                    <form class="d-inline" method="post" action="{{ route('admin.logout') }}">@csrf <button class="link-button">Salir</button></form>
                @else
                    <a href="{{ route('admin.login') }}">Administración</a>
                @endauth
            </div>
        </div>
    </nav>
    <main class="container">
        @foreach(['success','warning'] as $type)
            @if(session($type))
                <div class="alert alert-{{ $type }}">{{ session($type) }}</div>
            @endif
        @endforeach
        @yield('content')
    </main>
    @stack('scripts')
</body>
</html>
