<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Página de inicio - PROBIEN</title>
    <style>
        body{margin:0;min-height:100vh;display:flex;align-items:center;justify-content:center;background:#fff7ef;font-family:Inter,Segoe UI,system-ui,sans-serif;color:#3d2516}
        .box{width:100%;max-width:1200px;text-align:center;padding:2rem;border-radius:16px;min-height:calc(100vh - 96px);display:flex;align-items:center;justify-content:center;position:relative}
        .hero-content{position:relative;z-index:2;width:100%;min-height:320px}
        .logo-wrapper{position:absolute;left:50%;top:5%;transform:translate(-50%, -50%);}
        img.logo{height:120px;object-fit:contain;filter:drop-shadow(0 6px 12px rgba(0,0,0,.12))}
        .options{display:flex;flex-wrap:wrap;gap:1rem;justify-content:center;margin-top:1.25rem}
        .opt{display:inline-flex;align-items:center;justify-content:center;padding:1rem 1.25rem;border-radius:14px;background:rgba(255,255,255,.92);border:1px solid rgba(234,216,199,.9);font-weight:800;color:#b95712;text-decoration:none;min-width:200px;box-shadow:0 8px 20px rgba(57,24,0,.06);position:absolute;pointer-events:auto}
        .opt:hover{background:#fff4e9;transform:translateY(-3px);transition:transform .18s}
        .button-layer{position:absolute;inset:0;pointer-events:none}
        .page-buttons{position:relative;width:100%;height:100%}
        .hero-overlay{position:absolute;inset:0;background:linear-gradient(180deg,rgba(0,0,0,.08),rgba(0,0,0,.22));pointer-events:none}
        @if(($hp['mobile_layout'] ?? 'stacked') === 'stacked')
        @media (max-width: 767px) {
            body{align-items:flex-start}
            .box{padding:1rem;min-height:auto}
            .hero-content{min-height:auto}
            .logo-wrapper{position:static;transform:none;margin:0 auto}
            .button-layer{position:static;inset:auto;pointer-events:auto}
            .page-buttons{position:static;display:flex;flex-direction:column;align-items:center;gap:0.9rem;width:100%;max-width:420px;margin:0 auto;padding-top:1rem}
            .opt{position:static;transform:none;min-width:auto;width:100%;max-width:100%;border-radius:14px}
            .hero-overlay{background:linear-gradient(180deg,rgba(255,255,255,.28),rgba(255,255,255,.9));}
        }
        @else
        @media (max-width: 767px) {
            body{align-items:flex-start}
            .box{padding:1rem;min-height:auto}
            .hero-content{min-height:auto}
            .logo-wrapper{position:absolute;left:50%;top:5%;transform:translate(-50%, -50%)}
            .button-layer{position:absolute;inset:0;pointer-events:none}
            .page-buttons{position:relative;width:100%;height:100%}
            .opt{position:absolute;transform:translate(-50%,-50%);min-width:180px;max-width:240px}
            .hero-overlay{background:linear-gradient(180deg,rgba(0,0,0,.04),rgba(0,0,0,.22));}
        }
        @endif
    </style>
</head>
<body>
    @php
        $hpPath = storage_path('app/homepage.json');
        $hp = file_exists($hpPath) ? json_decode(file_get_contents($hpPath), true) : null;
        $collageLayout = $hp['collage_layout'] ?? (($hp['collage_rows'] ?? 2) . 'x' . ($hp['collage_columns'] ?? 4));
        [$collageRows, $collageColumns] = explode('x', $collageLayout) + [2, 4];
        $collageRows = max(1, min(4, (int) $collageRows));
        $collageColumns = max(1, min(6, (int) $collageColumns));
        $collageLayout = $collageRows . 'x' . $collageColumns;
    @endphp

    <div class="box" style="position:relative;overflow:hidden;border-radius:14px;padding:2.5rem;">
        @if($hp && !empty($hp['images']))
            @if($hp['mode'] === 'slideshow')
                <div style="position:absolute;inset:0;z-index:0;">
                    <style>
                        .slideshow{position:relative;height:100%;width:100%;overflow:hidden}
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
                <div style="position:absolute;left:0;top:0;right:0;bottom:0;z-index:0;display:grid;grid-template-columns:repeat({{ $collageColumns }},minmax(0,1fr));grid-auto-rows:1fr;gap:6px;padding:12px;">
                    @foreach($hp['images'] as $img)
                        <div style="width:100%;height:100%;overflow:hidden;border-radius:0;"><img src="{{ $img }}" style="width:100%;height:100%;object-fit:cover;opacity:.95"></div>
                    @endforeach
                </div>
            @endif
        @endif
        @if($hp && !empty($hp['images']))
            <div class="hero-overlay"></div>
        @endif

        @php
            $buttonPositions = is_array($hp['button_positions'] ?? null) ? $hp['button_positions'] : [];
            $logoPosition = is_array($hp['logo_position'] ?? null) ? $hp['logo_position'] : ['x' => 50, 'y' => 15];
            $logoX = isset($logoPosition['x']) ? $logoPosition['x'] : 50;
            $logoY = isset($logoPosition['y']) ? $logoPosition['y'] : 15;
            $logoHeight = $hp['logo_height'] ?? 120;
            $buttons = [
                ['key' => 'conocenos', 'label' => 'Conócenos', 'url' => '/conocenos'],
                ['key' => 'eventos', 'label' => 'Eventos realizados', 'url' => '/eventos'],
                ['key' => 'servicios', 'label' => 'Servicios', 'url' => '/servicios'],
                ['key' => 'market_research', 'label' => 'Investigación de Mercados', 'url' => route('market-research.index')],
            ];
        @endphp

        <div style="position:relative;z-index:2;width:100%;min-height:320px;">
            <div style="position:absolute;left:{{ $logoX }}%;top:{{ $logoY }}%;transform:translate(-50%,-50%);z-index:3;">
                <a href="{{ route('home') }}"><img class="logo" src="/images/probien-logo.png" alt="PROBIEN" style="height:{{ $logoHeight }}px"></a>
            </div>
            <div class="button-layer">
            <div class="page-buttons">
                @foreach($buttons as $button)
                    @php
                        $x = isset($buttonPositions[$button['key']]['x']) ? $buttonPositions[$button['key']]['x'] : 50;
                        $y = isset($buttonPositions[$button['key']]['y']) ? $buttonPositions[$button['key']]['y'] : 60;
                    @endphp
                    <a class="opt" href="{{ $button['url'] }}" style="left:{{ $x }}%;top:{{ $y }}%;z-index:3;">{{ $button['label'] }}</a>
                @endforeach
            </div>
        </div>
        </div>
    </div>
</body>
</html>
