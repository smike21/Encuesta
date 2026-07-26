@extends('layouts.app')

@section('title','Personalizar página principal')

@section('content')
<div class="mb-4"><h1>Personalizar página principal</h1><p>Sube imágenes para la página principal. Puedes combinarlas en collage o activarlas como slideshow. Arrastra para reordenar y usa el ícono para quitar.</p></div>

<form method="post" action="{{ route('admin.homepage.save') }}" enctype="multipart/form-data" id="homepage-form">
    @csrf
    <div class="mb-3">
        <label class="form-label">Modo de presentación</label>
        <div class="d-flex gap-3">
            <label><input type="radio" name="mode" value="collage" {{ (old('mode',$homepage['mode'] ?? 'collage') == 'collage') ? 'checked':'' }}> Collage</label>
            <label><input type="radio" name="mode" value="slideshow" {{ (old('mode',$homepage['mode'] ?? '') == 'slideshow') ? 'checked':'' }}> Presentación</label>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Opciones por modo</label>
        <div id="mode-options">
            <div id="mode-options-slideshow" style="display:{{ (old('mode',$homepage['mode'] ?? '') == 'slideshow') ? 'block' : 'none' }};">
                <div class="d-flex gap-3 align-items-center">
                    <select name="transition" id="transition" class="form-select">
                        <option value="fade" {{ ($homepage['transition'] ?? 'fade') == 'fade' ? 'selected':'' }}>Fundido</option>
                        <option value="slide" {{ ($homepage['transition'] ?? '') == 'slide' ? 'selected':'' }}>Deslizamiento</option>
                    </select>
                    <label class="small">Velocidad (segundos)</label>
                    <input type="number" name="speed" id="speed" min="1" max="30" value="{{ $homepage['speed'] ?? 4 }}" class="form-control" style="width:80px">
                </div>
            </div>
            <div id="mode-options-collage" style="display:{{ (old('mode',$homepage['mode'] ?? '') == 'collage') ? 'block' : 'none' }};">
                <div class="small text-muted">No hay opciones adicionales para Collage por ahora.</div>
            </div>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Imágenes actuales (arrastrar para reordenar)</label>
        <input type="hidden" name="existing_order" id="existing_order" value="">
        <div id="existing-list" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:12px">
            @foreach($homepage['images'] ?? [] as $i => $img)
                <div class="card hp-item" draggable="true" data-url="{{ $img }}" style="position:relative;">
                    <div class="card-body text-center p-2"><img src="{{ $img }}" style="max-width:100%;height:120px;object-fit:cover;border-radius:6px;"></div>
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <small class="small text-muted">Arrastrar</small>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-remove">Quitar</button>
                    </div>
                </div>
            @endforeach
        </div>
        <p class="small text-muted mt-2">Máximo {{ env('HOMEPAGE_MAX_IMAGES', 8) }} imágenes.</p>
    </div>

    <div class="mb-3">
        <label class="form-label">Subir nuevas imágenes (múltiples)</label>
        <input type="file" name="images[]" id="new-images" multiple accept="image/*" class="form-control">
        <div id="preview" style="margin-top:.75rem;display:grid;grid-template-columns:repeat(auto-fit,minmax(120px,1fr));gap:8px"></div>
    </div>

    <hr>
    <div class="mb-3">
        <label class="form-label">Logo del sitio</label>
        <input type="file" name="logo" accept="image/*" class="form-control">
        <div class="mt-2 small text-muted">Si subes un logo se reemplazará el actual.</div>
    </div>
    @php
        $logoPositionX = old('logo_position.x', data_get($homepage, 'logo_position.x', 50));
        $logoPositionY = old('logo_position.y', data_get($homepage, 'logo_position.y', 15));
    @endphp
    @php
        $previewMode = old('mode', $homepage['mode'] ?? 'collage');
        $mobileLayout = old('mobile_layout', $homepage['mobile_layout'] ?? 'stacked');
        $images = $homepage['images'] ?? [];
    @endphp
    <div class="mb-3">
        <label class="form-label">Tamaño del logo: <span id="logo_height_value">{{ $homepage['logo_height'] ?? 120 }}</span> px</label>
        <input type="range" name="logo_height" id="logo_height" min="60" max="220" value="{{ $homepage['logo_height'] ?? 120 }}" class="form-range">
        <div class="small text-muted">Ajusta el alto del logo en la página principal.</div>
    </div>
    <div class="mb-3">
        <label class="form-label">Posición del logo</label>
        <div class="d-flex flex-wrap gap-3 align-items-center">
            <label class="form-label" style="flex:1;min-width:180px;">X (%) <span id="logo_x_value">{{ $logoPositionX }}</span>
                <input type="range" name="logo_position[x]" id="logo_x" min="0" max="100" value="{{ $logoPositionX }}" class="form-range logo-position-input" data-axis="x">
            </label>
            <label class="form-label" style="flex:1;min-width:180px;">Y (%) <span id="logo_y_value">{{ $logoPositionY }}</span>
                <input type="range" name="logo_position[y]" id="logo_y" min="0" max="100" value="{{ $logoPositionY }}" class="form-range logo-position-input" data-axis="y">
            </label>
        </div>
        <div class="small text-muted">Controla la posición del logo en la página de inicio.</div>
    </div>
    <div class="mb-3">
        <label class="form-label">Posición individual de botones</label>
        @php
            $buttons = [
                'conocenos' => ['key' => 'conocenos', 'label' => 'Conócenos', 'url' => '/conocenos', 'x' => old('button_positions.conocenos.x', data_get($homepage, 'button_positions.conocenos.x', 40)), 'y' => old('button_positions.conocenos.y', data_get($homepage, 'button_positions.conocenos.y', 35))],
                'eventos' => ['key' => 'eventos', 'label' => 'Eventos realizados', 'url' => '/eventos', 'x' => old('button_positions.eventos.x', data_get($homepage, 'button_positions.eventos.x', 70)), 'y' => old('button_positions.eventos.y', data_get($homepage, 'button_positions.eventos.y', 35))],
                'servicios' => ['key' => 'servicios', 'label' => 'Servicios', 'url' => '/servicios', 'x' => old('button_positions.servicios.x', data_get($homepage, 'button_positions.servicios.x', 40)), 'y' => old('button_positions.servicios.y', data_get($homepage, 'button_positions.servicios.y', 55))],
                'market_research' => ['key' => 'market_research', 'label' => 'Investigación de Mercados', 'url' => route('market-research.index'), 'x' => old('button_positions.market_research.x', data_get($homepage, 'button_positions.market_research.x', 70)), 'y' => old('button_positions.market_research.y', data_get($homepage, 'button_positions.market_research.y', 55))],
            ];
        @endphp
        <div style="display:flex;flex-direction:column;gap:1rem;">
            <div id="preview-frame" style="width:100%;max-width:900px;margin:0 auto;padding:2rem;box-sizing:border-box;border:1px dashed #c6b99c;border-radius:16px;background:#fff7ef;overflow:hidden;aspect-ratio:16 / 9;min-height:480px;">
                <div style="position:relative;width:100%;aspect-ratio:16 / 9;min-height:480px;">
                    @if(!empty($images))
                        <style>
                            /* slideshow / collage base */
                            #preview-slideshow img, #preview-collage img {position:absolute;inset:0;width:100%;height:100%;object-fit:cover;}
                            #preview-slideshow img {opacity:0;transition:opacity .9s ease, transform .9s ease;}
                            #preview-slideshow img.active {opacity:1;transform:translateX(0);}
                            #preview-slideshow.slide img {transform:translateX(100%);}
                            #preview-slideshow.slide img.active {transform:translateX(0);}

                            /* Button & logo styles to match public home.blade.php */
                            #preview-frame .logo-wrapper{position:absolute;left:50%;top:5%;transform:translate(-50%,-50%);} 
                            #preview-frame img.logo{height:120px;object-fit:contain;filter:drop-shadow(0 6px 12px rgba(0,0,0,.12));}
                            #preview-frame .button-layer{position:absolute;inset:0;pointer-events:none}
                            #preview-frame .page-buttons{position:relative;width:100%;height:100%}
                            #preview-frame .opt{display:inline-flex;align-items:center;justify-content:center;padding:1rem 1.25rem;border-radius:14px;background:rgba(255,255,255,.92);border:1px solid rgba(234,216,199,.9);font-weight:800;color:#b95712;text-decoration:none;min-width:200px;box-shadow:0 8px 20px rgba(57,24,0,.06);position:absolute;pointer-events:auto}
                            #preview-frame .opt:hover{background:#fff4e9;transform:translateY(-3px);transition:transform .18s}
                            @if(($homepage['mobile_layout'] ?? 'stacked') === 'stacked')
                            @media (max-width: 767px) {
                                #preview-frame{padding:1rem}
                                #preview-frame .logo-wrapper{position:static;transform:none;margin:0 auto}
                                #preview-frame .button-layer{position:static;pointer-events:auto}
                                #preview-frame .page-buttons{position:static;display:flex;flex-direction:column;align-items:center;gap:0.9rem;width:100%;max-width:420px;margin:0 auto;padding-top:1rem}
                                #preview-frame .opt{position:static;transform:none;min-width:auto;width:100%;max-width:100%;border-radius:14px}
                            }
                            @else
                            @media (max-width: 767px) {
                                #preview-frame{padding:1rem}
                                #preview-frame .logo-wrapper{position:absolute;left:50%;top:5%;transform:translate(-50%,-50%)}
                                #preview-frame .button-layer{position:absolute;inset:0;pointer-events:none}
                                #preview-frame .page-buttons{position:relative;width:100%;height:100%}
                                #preview-frame .opt{position:absolute;transform:translate(-50%,-50%);min-width:180px;max-width:240px}
                            }
                            @endif
                        </style>
                        <div id="preview-slideshow" style="position:absolute;inset:0;z-index:0;overflow:hidden;display:{{ $previewMode === 'slideshow' ? 'block' : 'none' }};">
                            @foreach($images as $i => $img)
                                <img src="{{ $img }}" class="{{ $i===0 ? 'active' : '' }}">
                            @endforeach
                        </div>
                        <div id="preview-collage" style="position:absolute;inset:0;z-index:0;display:{{ $previewMode === 'slideshow' ? 'none' : 'grid' }};grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:6px;padding:12px;">
                            @foreach($images as $img)
                                <div style="overflow:hidden;border-radius:12px;"><img src="{{ $img }}"></div>
                            @endforeach
                        </div>
                        <div style="position:absolute;inset:0;background:linear-gradient(180deg,rgba(0,0,0,.08),rgba(0,0,0,.22));pointer-events:none;z-index:1;"></div>
                    @endif
                    <div id="preview-logo-container" class="logo-wrapper" style="position:absolute;left:{{ $logoPositionX }}%;top:{{ $logoPositionY }}%;transform:translate(-50%,-50%);display:flex;align-items:center;justify-content:center;padding:.35rem .75rem;border-radius:999px;background:#fff;box-shadow:0 12px 20px rgba(0,0,0,.12);z-index:2;">
                        <img id="preview-logo-img" class="logo" src="/images/probien-logo.png" alt="Logo" style="height:{{ $homepage['logo_height'] ?? 120 }}px;max-width:220px;object-fit:contain;" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <span id="preview-logo-text" class="small text-muted" style="display:none;">Logo</span>
                    </div>
                    @foreach($buttons as $key => $button)
                        <a id="preview-{{ $key }}" class="opt" href="{{ $button['url'] ?? '#' }}" style="position:absolute;left:{{ $button['x'] }}%;top:{{ $button['y'] }}%;transform:translate(-50%,-50%);min-width:200px;padding:1rem 1.25rem;background:#b95712;color:#fff;border-radius:14px;font-weight:800;font-size:.9rem;white-space:nowrap;box-shadow:0 8px 20px rgba(57,24,0,.06);">{{ $button['label'] }}</a>
                    @endforeach
                </div>
            </div>
            @foreach($buttons as $key => $button)
                <div class="mb-2" style="padding:1rem;border:1px solid #e9e0d3;border-radius:14px;background:#fff;">
                    <strong>{{ $button['label'] }}</strong>
                    <div class="d-flex flex-wrap gap-3 align-items-center mt-2">
                        <label class="form-label" style="flex:1;min-width:180px;">X (%) <span id="{{ $key }}_x_value">{{ $button['x'] }}</span>
                            <input type="range" name="button_positions[{{ $key }}][x]" id="{{ $key }}_x" min="0" max="100" value="{{ $button['x'] }}" class="form-range button-position-input" data-key="{{ $key }}" data-axis="x">
                        </label>
                        <label class="form-label" style="flex:1;min-width:180px;">Y (%) <span id="{{ $key }}_y_value">{{ $button['y'] }}</span>
                            <input type="range" name="button_positions[{{ $key }}][y]" id="{{ $key }}_y" min="0" max="100" value="{{ $button['y'] }}" class="form-range button-position-input" data-key="{{ $key }}" data-axis="y">
                        </label>
                    </div>
                </div>
            @endforeach
            <div class="small text-muted">Ajusta la posición de cada botón individualmente. Los valores se guardan en porcentaje para adaptarse al tamaño de pantalla.</div>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label">Personalizar móviles</label>
        <div class="d-flex flex-column flex-md-row gap-3 align-items-start">
            <label class="d-flex align-items-center gap-2"><input type="radio" name="mobile_layout" value="stacked" {{ $mobileLayout === 'stacked' ? 'checked' : '' }}> Botones apilados</label>
            <label class="d-flex align-items-center gap-2"><input type="radio" name="mobile_layout" value="absolute" {{ $mobileLayout === 'absolute' ? 'checked' : '' }}> Posición personalizada</label>
        </div>
        <div class="small text-muted">Elige el diseño que prefieres para los celulares.</div>
    </div>
    <div class="mb-3">
        <label class="form-label">Vista previa móvil</label>
        <div id="mobile-preview" style="width:100%;max-width:420px;margin:auto;border:1px dashed #c6b99c;border-radius:16px;background:#fff7ef;overflow:hidden;padding:1rem;box-sizing:border-box;min-height:520px;position:relative;">
            @if(!empty($images))
                <div style="position:absolute;inset:0;z-index:0;display:grid;grid-template-columns:repeat(auto-fit,minmax(80px,1fr));gap:6px;padding:12px;">
                    @foreach($images as $img)
                        <div style="overflow:hidden;border-radius:10px;"><img src="{{ $img }}" style="width:100%;height:100%;object-fit:cover;"></div>
                    @endforeach
                </div>
                <div style="position:absolute;inset:0;background:linear-gradient(180deg,rgba(0,0,0,.06),rgba(255,255,255,.9));pointer-events:none;z-index:1;"></div>
            @endif
            @if($mobileLayout === 'absolute')
                <div style="position:relative;z-index:2;width:100%;height:100%;">
                    <div style="position:absolute;left:{{ $logoPositionX }}%;top:{{ $logoPositionY }}%;transform:translate(-50%,-50%);display:flex;align-items:center;justify-content:center;padding:.4rem .8rem;border-radius:999px;background:#fff;box-shadow:0 12px 20px rgba(0,0,0,.12);">
                        <img src="/images/probien-logo.png" alt="Logo" style="height:{{ $homepage['logo_height'] ?? 120 }}px;max-width:180px;object-fit:contain;">
                    </div>
                    @foreach($buttons as $key => $button)
                        @php
                            $x = old('button_positions.'.$key.'.x', data_get($homepage, 'button_positions.'.$key.'.x', 50));
                            $y = old('button_positions.'.$key.'.y', data_get($homepage, 'button_positions.'.$key.'.y', 60));
                        @endphp
                        <a href="{{ $button['url'] ?? '#' }}" style="position:absolute;left:{{ $x }}%;top:{{ $y }}%;transform:translate(-50%,-50%);display:inline-flex;align-items:center;justify-content:center;min-width:160px;padding:1rem 1.25rem;border-radius:14px;background:rgba(255,255,255,.92);border:1px solid rgba(234,216,199,.9);font-weight:800;color:#b95712;text-decoration:none;box-shadow:0 8px 20px rgba(57,24,0,.06);z-index:2;">{{ $button['label'] }}</a>
                    @endforeach
                </div>
            @else
                <div style="position:relative;z-index:2;display:flex;flex-direction:column;gap:0.9rem;align-items:center;">
                    <div style="display:flex;justify-content:center;width:100%;">
                        <img src="/images/probien-logo.png" alt="Logo" style="height:{{ $homepage['logo_height'] ?? 120 }}px;max-width:180px;object-fit:contain;">
                    </div>
                    <div id="mobile-buttons-preview" style="width:100%;display:flex;flex-direction:column;gap:.9rem;">
                        @foreach($buttons as $button)
                            <a href="{{ $button['url'] ?? '#' }}" style="display:inline-flex;align-items:center;justify-content:center;padding:1rem 1.25rem;border-radius:14px;background:rgba(255,255,255,.92);border:1px solid rgba(234,216,199,.9);font-weight:800;color:#b95712;text-decoration:none;min-width:100%;box-shadow:0 8px 20px rgba(57,24,0,.06);">{{ $button['label'] }}</a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="d-flex gap-2">
        <button class="btn btn-primary">Guardar</button>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">Cancelar</a>
    </div>
    </form>

<script>
    // Drag & drop reorder
    (function(){
        const list = document.getElementById('existing-list');
        let dragEl = null;
        function updateExistingOrder(){
            const items = Array.from(list.querySelectorAll('.hp-item'));
            const arr = items.map(it => ({url: it.dataset.url, keep: !it.classList.contains('removed')}));
            document.getElementById('existing_order').value = JSON.stringify(arr);
        }
        list.addEventListener('dragstart', (e)=>{ if(e.target.classList.contains('hp-item')) { dragEl = e.target; e.dataTransfer.effectAllowed='move'; }});
        list.addEventListener('dragover', (e)=>{ e.preventDefault(); const after = e.target.closest('.hp-item'); if(after && after !== dragEl) { list.insertBefore(dragEl, after.nextSibling); }});
        list.addEventListener('dragend', ()=>{ dragEl=null; updateExistingOrder(); });
        list.querySelectorAll('.btn-remove').forEach(btn=>btn.addEventListener('click', (e)=>{ const card = e.target.closest('.hp-item'); card.classList.toggle('removed'); card.style.opacity = card.classList.contains('removed') ? '.45' : '1'; updateExistingOrder(); }));
        updateExistingOrder();
    })();

    // Preview new uploads
    document.getElementById('new-images').addEventListener('change', function(e){
        const preview = document.getElementById('preview'); preview.innerHTML='';
        const files = Array.from(e.target.files);
        files.forEach(f=>{
            const reader = new FileReader();
            const el = document.createElement('div'); el.style.overflow='hidden'; el.style.borderRadius='6px'; el.style.height='100px';
            reader.onload = (ev)=>{ el.innerHTML = '<img src="'+ev.target.result+'" style="width:100%;height:100%;object-fit:cover">'; };
            reader.readAsDataURL(f); preview.appendChild(el);
        });
    });

    // Ensure existing_order is up-to-date before submit
    document.getElementById('homepage-form').addEventListener('submit', function(){ if(!document.getElementById('existing_order').value) { document.getElementById('existing_order').value = '[]'; } });

    const buttonInputs = document.querySelectorAll('.button-position-input');

    function updatePreview(input) {
        const key = input.dataset.key;
        const axis = input.dataset.axis;
        const value = input.value;
        const preview = document.getElementById('preview-' + key);
        const valueDisplay = document.getElementById(key + '_' + axis + '_value');
        if (preview) {
            if (axis === 'x') preview.style.left = value + '%';
            if (axis === 'y') preview.style.top = value + '%';
        }
        if (valueDisplay) {
            valueDisplay.textContent = value;
        }
    }

    buttonInputs.forEach(input => {
        input.addEventListener('input', () => updatePreview(input));
    });

    const logoHeightInput = document.getElementById('logo_height');
    const logoHeightValue = document.getElementById('logo_height_value');
    const previewLogoImg = document.getElementById('preview-logo-img');
    const previewLogoContainer = document.getElementById('preview-logo-container');
    const logoPositionInputs = document.querySelectorAll('.logo-position-input');

    if (logoHeightInput) {
        logoHeightInput.addEventListener('input', function () {
            const value = this.value;
            if (logoHeightValue) {
                logoHeightValue.textContent = value;
            }
            if (previewLogoImg) {
                previewLogoImg.style.height = value + 'px';
            }
        });
    }

    function updateLogoPreview(input) {
        const axis = input.dataset.axis;
        const value = input.value;
        const label = document.getElementById('logo_' + axis + '_value');
        if (label) {
            label.textContent = value;
        }
        if (previewLogoContainer) {
            if (axis === 'x') previewLogoContainer.style.left = value + '%';
            if (axis === 'y') previewLogoContainer.style.top = value + '%';
        }
    }

    logoPositionInputs.forEach(input => {
        input.addEventListener('input', () => updateLogoPreview(input));
    });

    const mobileLayoutInputs = document.querySelectorAll('input[name="mobile_layout"]');
    const stackedPreview = document.getElementById('mobile-preview-stacked');
    const absolutePreview = document.getElementById('mobile-preview-absolute');

    function updateMobilePreview() {
        const selected = document.querySelector('input[name="mobile_layout"]:checked')?.value || 'stacked';
        if (stackedPreview && absolutePreview) {
            stackedPreview.style.display = selected === 'stacked' ? 'flex' : 'none';
            absolutePreview.style.display = selected === 'absolute' ? 'block' : 'none';
        }
    }

    mobileLayoutInputs.forEach(input => {
        input.addEventListener('change', updateMobilePreview);
    });

    const modeInputs = document.querySelectorAll('input[name="mode"]');
    const transitionSelect = document.querySelector('select[name="transition"]');
    const speedInput = document.querySelector('input[name="speed"]');
    let slideshowTimer = null;

    function setPreviewTransition(transition) {
        const slideshow = document.getElementById('preview-slideshow');
        if (!slideshow) return;
        if (transition === 'slide') {
            slideshow.classList.add('slide');
        } else {
            slideshow.classList.remove('slide');
        }
    }

    function resetSlideshow() {
        const slideshow = document.getElementById('preview-slideshow');
        if (!slideshow) return;
        const imgs = Array.from(slideshow.querySelectorAll('img'));
        imgs.forEach((img, index) => {
            img.classList.toggle('active', index === 0);
        });
    }

    function startSlideshowPreview() {
        const slideshow = document.getElementById('preview-slideshow');
        if (!slideshow) return;
        const imgs = Array.from(slideshow.querySelectorAll('img'));
        if (imgs.length <= 1) return;
        let index = imgs.findIndex(img => img.classList.contains('active'));
        if (index < 0) index = 0;
        const transition = transitionSelect?.value || 'fade';
        const speed = parseInt(speedInput?.value || '4', 10) * 1000;
        setPreviewTransition(transition);
        if (slideshowTimer) clearInterval(slideshowTimer);
        slideshowTimer = setInterval(() => {
            imgs[index].classList.remove('active');
            index = (index + 1) % imgs.length;
            imgs[index].classList.add('active');
        }, Math.max(1000, speed));
    }

    function stopSlideshowPreview() {
        if (slideshowTimer) {
            clearInterval(slideshowTimer);
            slideshowTimer = null;
        }
        resetSlideshow();
    }

    function updatePreviewMode() {
        const selectedMode = document.querySelector('input[name="mode"]:checked')?.value || 'collage';
        const slideshow = document.getElementById('preview-slideshow');
        const collage = document.getElementById('preview-collage');
        if (slideshow && collage) {
            slideshow.style.display = selectedMode === 'slideshow' ? 'block' : 'none';
            collage.style.display = selectedMode === 'slideshow' ? 'none' : 'grid';
        }
        if (selectedMode === 'slideshow') {
            startSlideshowPreview();
        } else {
            stopSlideshowPreview();
        }
    }

    function updateModeOptions() {
        const selected = document.querySelector('input[name="mode"]:checked')?.value || 'collage';
        const slideshowOpts = document.getElementById('mode-options-slideshow');
        const collageOpts = document.getElementById('mode-options-collage');
        if (slideshowOpts && collageOpts) {
            slideshowOpts.style.display = selected === 'slideshow' ? 'block' : 'none';
            collageOpts.style.display = selected === 'collage' ? 'block' : 'none';
        }
    }

    modeInputs.forEach(input => input.addEventListener('change', () => { updatePreviewMode(); updateModeOptions(); }));
    transitionSelect?.addEventListener('change', () => {
        setPreviewTransition(transitionSelect.value);
    });
    speedInput?.addEventListener('input', () => {
        if (document.querySelector('input[name="mode"]:checked')?.value === 'slideshow') {
            startSlideshowPreview();
        }
    });

    updateMobilePreview();
    updatePreviewMode();
    updateModeOptions();
</script>

@endsection
