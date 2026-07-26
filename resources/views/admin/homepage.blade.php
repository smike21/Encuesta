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
    <div class="mb-3">
        <label class="form-label">Tamaño del logo</label>
        @php $lh = $homepage['logo_height'] ?? 120; $logoSize = $lh <= 90 ? 'small' : ($lh >= 150 ? 'large' : 'medium'); @endphp
        <select name="logo_size" class="form-select">
            <option value="small" {{ $logoSize=='small' ? 'selected':'' }}>Pequeño</option>
            <option value="medium" {{ $logoSize=='medium' ? 'selected':'' }}>Mediano</option>
            <option value="large" {{ $logoSize=='large' ? 'selected':'' }}>Grande</option>
        </select>
    </div>
    <div class="mb-3">
        <label class="form-label">Posición libre de botones</label>
        @php
            $buttonX = old('button_x', $homepage['button_x'] ?? 50);
            $buttonY = old('button_y', $homepage['button_y'] ?? 60);
        @endphp
        <div style="display:flex;flex-direction:column;gap:.8rem;">
            <div style="position:relative;width:100%;min-height:180px;border:1px dashed #c6b99c;border-radius:16px;background:#fff7ef;overflow:hidden;">
                <div id="button-preview" style="position:absolute;left:{{ $buttonX }}%;top:{{ $buttonY }}%;transform:translate(-50%,-50%);">
                    <div style="display:inline-flex;align-items:center;justify-content:center;padding:.7rem 1rem;background:#b95712;color:#fff;border-radius:999px;font-weight:800;box-shadow:0 8px 20px rgba(0,0,0,.14);">Botones</div>
                </div>
            </div>
            <div class="d-flex flex-wrap gap-3 align-items-center">
                <label class="form-label" style="flex:1;min-width:180px;">X (%) <span id="button_x_value">{{ $buttonX }}</span>
                    <input type="range" name="button_x" id="button_x" min="0" max="100" value="{{ $buttonX }}" class="form-range">
                </label>
                <label class="form-label" style="flex:1;min-width:180px;">Y (%) <span id="button_y_value">{{ $buttonY }}</span>
                    <input type="range" name="button_y" id="button_y" min="0" max="100" value="{{ $buttonY }}" class="form-range">
                </label>
            </div>
            <div class="small text-muted">Arrastra los controles para ubicar los botones libremente. Esta posición se guarda en porcentaje de la pantalla.</div>
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

    const buttonX = document.getElementById('button_x');
    const buttonY = document.getElementById('button_y');
    const buttonPreview = document.getElementById('button-preview');
    const buttonXValue = document.getElementById('button_x_value');
    const buttonYValue = document.getElementById('button_y_value');

    function updatePreview() {
        const x = buttonX.value;
        const y = buttonY.value;
        buttonPreview.style.left = x + '%';
        buttonPreview.style.top = y + '%';
        buttonXValue.textContent = x;
        buttonYValue.textContent = y;
    }

    buttonX.addEventListener('input', updatePreview);
    buttonY.addEventListener('input', updatePreview);
</script>

@endsection
