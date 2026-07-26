<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Página de inicio - PROBIEN</title>
    <style>
        body{margin:0;height:100vh;display:flex;align-items:center;justify-content:center;background:#fff7ef;font-family:Inter,Segoe UI,system-ui,sans-serif;color:#3d2516}
        .box{width:min(920px,94%);text-align:center;padding:2rem;border-radius:16px}
        img.logo{height:120px;object-fit:contain;margin-bottom:1.5rem;filter:drop-shadow(0 6px 12px rgba(0,0,0,.12))}
        .options{display:flex;flex-wrap:wrap;gap:1rem;justify-content:center;margin-top:1.25rem}
        .opt{display:inline-flex;align-items:center;justify-content:center;padding:1rem 1.25rem;border-radius:14px;background:rgba(255,255,255,.92);border:1px solid rgba(234,216,199,.9);font-weight:800;color:#b95712;text-decoration:none;min-width:200px;box-shadow:0 8px 20px rgba(57,24,0,.06)}
        .opt:hover{background:#fff4e9;transform:translateY(-3px);transition:transform .18s}
        .hero-overlay{position:absolute;inset:0;background:linear-gradient(180deg,rgba(0,0,0,.08),rgba(0,0,0,.22));pointer-events:none}
    </style>
</head>
<body>
    @php
        $hpPath = storage_path('app/homepage.json');
        $hp = file_exists($hpPath) ? json_decode(file_get_contents($hpPath), true) : null;
    @endphp

    <div class="box" style="position:relative;overflow:hidden;border-radius:14px;padding:2.5rem;">
        @if($hp && !empty($hp['images']))
            @if($hp['mode'] === 'slideshow')
                <div style="position:absolute;inset:0;z-index:0;">
                    <style>
                        .slideshow{position:relative;height:320px;overflow:hidden}
                        .slideshow img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:0;transition:opacity 1s, transform 1s}
                        .slideshow img.active{opacity:1}
                        .slideshow.slide img{transform:translateX(100%)}
                        .slideshow.slide img.active{transform:translateX(0)}
                    </style>
                    <div class="slideshow">
                        @foreach($hp['images'] as $i => $img)
                            <img src="{{ $img }}" class="{{ $i===0 ? 'active':'' }}">
                        @endforeach
                    </div>
                    <script>
                        (function(){
                            const container = document.querySelector('.slideshow');
                            const imgs = document.querySelectorAll('.slideshow img');
                            if(!imgs.length) return;
                            const transition = '{{ $hp['transition'] ?? 'fade' }}';
                            const speed = parseInt('{{ $hp['speed'] ?? 4 }}',10) * 1000;
                            if(transition === 'slide') container.classList.add('slide');
                            let idx=0;
                            setInterval(()=>{ imgs[idx].classList.remove('active'); idx=(idx+1)%imgs.length; imgs[idx].classList.add('active'); }, Math.max(1000, speed));
                        })();
                    </script>
                </div>
            @else
                <div style="position:absolute;inset:0;z-index:0;display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:6px;padding:12px;">
                    @foreach($hp['images'] as $img)
                        <div style="width:100%;height:100%;overflow:hidden;border-radius:8px;"><img src="{{ $img }}" style="width:100%;height:100%;object-fit:cover;opacity:.95"></div>
                    @endforeach
                </div>
            @endif
        @endif
        @if($hp && !empty($hp['images']))
            <div class="hero-overlay"></div>
        @endif

        <div style="position:relative;z-index:2;">
            <img class="logo" src="/images/probien-logo.png" alt="PROBIEN">
            <div class="options">
                <a class="opt" href="/conocenos">Conócenos</a>
                <a class="opt" href="/eventos">Eventos realizados</a>
                <a class="opt" href="/servicios">Servicios</a>
                <a class="opt" href="{{ route('market-research.index') }}">Investigación de Mercados</a>
            </div>
        </div>
    </div>
</body>
</html>
