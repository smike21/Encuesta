<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Página de inicio - PROBIEN</title>
    <style>
        body{margin:0;height:100vh;display:flex;align-items:center;justify-content:center;background:#fff7ef;font-family:Inter,Segoe UI,system-ui,sans-serif;color:#3d2516}
        .box{width:min(720px,90%);text-align:center;padding:2rem}
        img.logo{height:140px;object-fit:contain;margin-bottom:1.5rem}
        .options{display:grid;gap:.75rem;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));margin-top:1rem}
        .opt{display:inline-block;padding:.9rem 1.1rem;border-radius:12px;background:#fff;border:1px solid #ead8c7;font-weight:800;color:#b95712;text-decoration:none}
        .opt:hover{background:#fff4e9}
    </style>
</head>
<body>
    <div class="box">
        <img class="logo" src="/images/probien-logo.png" alt="PROBIEN">
        <div class="options">
            <a class="opt" href="/conocenos">Conócenos</a>
            <a class="opt" href="/eventos">Eventos realizados</a>
            <a class="opt" href="/servicios">Servicios</a>
            <a class="opt" href="{{ route('market-research.index') }}">Investigación de Mercados</a>
        </div>
    </div>
</body>
</html>
