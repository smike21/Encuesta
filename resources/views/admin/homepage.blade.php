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
        <label class="form-label">Transición</label>
        <div class="d-flex gap-3 align-items-center">
            <select name="transition" id="transition" class="form-select">
                <option value="fade" {{ ($homepage['transition'] ?? 'fade') == 'fade' ? 'selected':'' }}>Fundido</option>
                <option value="slide" {{ ($homepage['transition'] ?? '') == 'slide' ? 'selected':'' }}>Deslizamiento</option>
            </select>
            <label class="small">Velocidad (segundos)</label>
            <input type="number" name="speed" min="1" max="30" value="{{ $homepage['speed'] ?? 4 }}" class="form-control" style="width:80px">
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
        $logoPositionX = old('logo_position.x', $homepage['logo_position']['x'] ?? 50);
        $logoPositionY = old('logo_position.y', $homepage['logo_position']['y'] ?? 15);
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
                'conocenos' => ['label' => 'Conócenos', 'x' => old('button_positions.conocenos.x', $homepage['button_positions']['conocenos']['x'] ?? 40), 'y' => old('button_positions.conocenos.y', $homepage['button_positions']['conocenos']['y'] ?? 35)],
                'eventos' => ['label' => 'Eventos realizados', 'x' => old('button_positions.eventos.x', $homepage['button_positions']['eventos']['x'] ?? 70), 'y' => old('button_positions.eventos.y', $homepage['button_positions']['eventos']['y'] ?? 35)],
                'servicios' => ['label' => 'Servicios', 'x' => old('button_positions.servicios.x', $homepage['button_positions']['servicios']['x'] ?? 40), 'y' => old('button_positions.servicios.y', $homepage['button_positions']['servicios']['y'] ?? 55)],
                'market_research' => ['label' => 'Investigación de Mercados', 'x' => old('button_positions.market_research.x', $homepage['button_positions']['market_research']['x'] ?? 70), 'y' => old('button_positions.market_research.y', $homepage['button_positions']['market_research']['y'] ?? 55)],
            ];
        @endphp
        <div style="display:flex;flex-direction:column;gap:1rem;">
            <div style="width:100%;max-width:1200px;margin:0 auto;padding:2rem;box-sizing:border-box;border:1px dashed #c6b99c;border-radius:16px;background:#fff7ef;overflow:hidden;">
                <div style="position:relative;width:100%;min-height:320px;">
                    <div id="preview-logo-container" style="position:absolute;left:{{ $logoPositionX }}%;top:{{ $logoPositionY }}%;transform:translate(-50%,-50%);display:flex;align-items:center;justify-content:center;padding:.35rem .75rem;border-radius:999px;background:#fff;box-shadow:0 12px 20px rgba(0,0,0,.12);">
                        <img id="preview-logo-img" src="/images/probien-logo.png" alt="Logo" style="height:{{ $homepage['logo_height'] ?? 120 }}px;max-width:220px;object-fit:contain;" onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                        <span id="preview-logo-text" class="small text-muted" style="display:none;">Logo</span>
                    </div>
                    @foreach($buttons as $key => $button)
                        <div id="preview-{{ $key }}" style="position:absolute;left:{{ $button['x'] }}%;top:{{ $button['y'] }}%;transform:translate(-50%,-50%);padding:.55rem 1rem;background:#b95712;color:#fff;border-radius:999px;font-weight:800;font-size:.9rem;white-space:nowrap;box-shadow:0 6px 16px rgba(0,0,0,.12);">{{ $button['label'] }}</div>
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
</script>

@endsection
