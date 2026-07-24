@extends('layouts.app')
@section('title','Crear encuesta')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <h1>Crear encuesta</h1>
        <form id="survey-form" class="card p-4" method="post" action="{{ route('admin.store') }}" enctype="multipart/form-data">
            @csrf
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <label class="form-label">Título</label>
            <input class="form-control mb-3" name="title" required>

            <label class="form-label">Descripción</label>
            <textarea class="form-control mb-3" name="description"></textarea>

            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" value="1" name="collect_location" id="location">
                <label class="form-check-label" for="location">Solicitar ubicación al responder</label>
            </div>

            <h2 class="h4">Preguntas</h2>
            <div id="questions"></div>
            <button type="button" class="btn btn-outline-primary my-3" id="add">Agregar pregunta</button>
            <button class="btn btn-primary w-100">Guardar encuesta</button>
        </form>
    </div>
</div>
@endsection
@push('styles')
<style>
    .question-card { background: #fffdfb; }
    .question-card .question-top { display:flex; align-items:center; justify-content:space-between; gap:1rem; }
    .question-card .question-top .title-chip { display:inline-flex; align-items:center; gap:.45rem; padding:.35rem .7rem; border-radius:999px; background:#fff1e4; color:#8e3d08; font-size:.78rem; font-weight:800; }
    .options-editor { border:1px solid #ead8c7; border-radius:16px; padding:1rem; background:#fff8f2; margin-bottom:1rem; }
    .options-list { display:grid; gap:.65rem; }
    .option-row { display:grid; grid-template-columns:1fr auto; gap:.6rem; align-items:start; }
    .option-row input { width:100%; }
    .option-pill { border:0; background:#fff; border:1px solid #dfc8b6; border-radius:10px; padding:.55rem .8rem; display:inline-flex; align-items:center; gap:.45rem; font:inherit; color:#8e3d08; cursor:pointer; }
    .option-pill--danger { color:#9a2020; }
    .option-media-wrap { display:flex; align-items:center; gap:.6rem; margin-top:.45rem; }
    .option-file-input { display:none; }
    .option-file-label { min-width:140px; }
    .img-preview { width:48px; height:48px; object-fit:cover; border-radius:8px; margin-right:.5rem; border:1px solid #e6d7c7; }
    .image-previews { display:flex; align-items:center; margin-top:.5rem; }
    .image-controls { display:inline-flex; align-items:center; gap:.4rem; }
</style>
@endpush
@push('scripts')
<script>
    let n = 0;
    const box = document.getElementById('questions');

    function setOptionVisibility(questionIndex, isMultiple) {
        const optionsEditor = document.querySelector(`[data-options-editor="${questionIndex}"]`);
        const optionImages = document.querySelector(`[data-option-images="${questionIndex}"]`);
        const maxSelectionsWrap = document.querySelector(`[data-max-selections-wrap="${questionIndex}"]`);
        if (optionsEditor) optionsEditor.hidden = !isMultiple;
        if (optionImages) optionImages.hidden = !isMultiple;
        if (maxSelectionsWrap) maxSelectionsWrap.hidden = !isMultiple;
    }

    function addOption(questionIndex) {
        const list = document.querySelector(`[data-options-list="${questionIndex}"]`);
        const optionIndex = list.querySelectorAll('.option-row').length;
        const row = document.createElement('div');
        row.className = 'option-row';
        row.innerHTML = `
            <div>
                <input class="form-control" type="text" name="questions[${questionIndex}][options][]" placeholder="Escribe una opción" required>
                <div class="d-flex align-items-center gap-2 mt-2">
                    <span class="image-controls" data-question-index="${questionIndex}" hidden>
                        <button type="button" class="option-pill add-image-btn" data-question-index="${questionIndex}" data-option-index="${optionIndex}">Añadir imagen</button>
                    </span>
                    <div class="option-image-container" data-question-index="${questionIndex}" data-option-index="${optionIndex}" hidden>
                        <div class="option-media-wrap">
                            <label class="option-pill option-file-label">
                                <span>🖼️ Poner imagen</span>
                                <input class="option-file-input" type="file" accept="image/*" name="questions[${questionIndex}][option_images][${optionIndex}]">
                            </label>
                            <div><small class="text-success option-image-status" data-question-index="${questionIndex}" data-option-index="${optionIndex}" hidden>Foto subida</small></div>
                        </div>
                        <div class="image-previews"></div>
                    </div>
                </div>
            </div>
            <button type="button" class="option-pill option-pill--danger remove-option">Eliminar</button>
        `;
        list.appendChild(row);

        row.querySelector('.remove-option').addEventListener('click', () => row.remove());
    }

    function addQuestion() {
        const i = n++;
        box.insertAdjacentHTML('beforeend', `
            <div class="border rounded p-3 mb-3 question-card">
                <div class="question-top mb-3">
                    <span class="title-chip">Pregunta</span>
                    <div class="d-flex align-items-center" style="gap:.6rem">
                        <label class="form-check form-switch mb-0" style="margin-bottom:0">
                            <input class="form-check-input think-image-toggle" type="checkbox" data-question-index="${i}">
                            <span class="form-check-label">¿Piensa poner imagen?</span>
                        </label>
                        <button type="button" class="btn-close float-end" onclick="this.parentElement.parentElement.remove()"></button>
                    </div>
                </div>

                <label class="form-label">Texto de la pregunta</label>
                <input class="form-control mb-2" name="questions[${i}][text]" required>

                <label class="form-label">Tipo</label>
                <select class="form-select mb-3 question-type" data-question-index="${i}" name="questions[${i}][type]">
                    <option value="text">Respuesta corta</option>
                    <option value="paragraph">Párrafo</option>
                    <option value="multiple_choice">Opción múltiple</option>
                    <option value="scale">Escala (1-5)</option>
                </select>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" value="1" name="questions[${i}][is_required]" id="required_${i}" checked>
                    <label class="form-check-label" for="required_${i}">Pregunta obligatoria</label>
                </div>

                <div class="options-editor" data-options-editor="${i}" hidden>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label mb-0">Opciones</label>
                        <button type="button" class="option-pill add-option">Agregar opción</button>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input allow-multiple" type="checkbox" value="1" name="questions[${i}][allow_multiple]" id="allow_multiple_${i}">
                        <label class="form-check-label" for="allow_multiple_${i}">Permitir varias opciones</label>
                    </div>

                    <div class="mb-3" data-max-selections-wrap="${i}" hidden>
                        <label class="form-label">Número máximo de opciones permitidas</label>
                        <input class="form-control" type="number" min="1" name="questions[${i}][max_selections]" value="1" placeholder="Ej. 2">
                    </div>

                    <div class="options-list" data-options-list="${i}">
                        <div class="option-row">
                            <div>
                                <input class="form-control" type="text" name="questions[${i}][options][]" placeholder="Escribe una opción" required>
                                <div class="d-flex align-items-center gap-2 mt-2">
                                    <span class="image-controls" data-question-index="${i}" hidden>
                                        <button type="button" class="option-pill add-image-btn" data-question-index="${i}" data-option-index="0">Añadir imagen</button>
                                    </span>
                                    <div class="option-image-container" data-question-index="${i}" data-option-index="0" hidden>
                                        <div class="option-media-wrap">
                                            <label class="option-pill option-file-label">
                                                <span>🖼️ Poner imagen</span>
                                                <input class="option-file-input" type="file" accept="image/*" name="questions[${i}][option_images][0]">
                                            </label>
                                            <div><small class="text-success option-image-status" data-question-index="${i}" data-option-index="0" hidden>Foto subida</small></div>
                                        </div>
                                        <div class="image-previews"></div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="option-pill option-pill--danger remove-option">Eliminar</button>
                        </div>
                        <div class="option-row">
                            <div>
                                <input class="form-control" type="text" name="questions[${i}][options][]" placeholder="Escribe una opción" required>
                                <div class="d-flex align-items-center gap-2 mt-2">
                                    <span class="image-controls" data-question-index="${i}" hidden>
                                        <button type="button" class="option-pill add-image-btn" data-question-index="${i}" data-option-index="1">Añadir imagen</button>
                                    </span>
                                    <div class="option-image-container" data-question-index="${i}" data-option-index="1" hidden>
                                        <div class="option-media-wrap">
                                            <label class="option-pill option-file-label">
                                                <span>🖼️ Poner imagen</span>
                                                <input class="option-file-input" type="file" accept="image/*" name="questions[${i}][option_images][1]">
                                            </label>
                                            <div><small class="text-success option-image-status" data-question-index="${i}" data-option-index="1" hidden>Foto subida</small></div>
                                        </div>
                                        <div class="image-previews"></div>
                                    </div>
                                </div>
                            </div>
                            <button type="button" class="option-pill option-pill--danger remove-option">Eliminar</button>
                        </div>
                    </div>
                </div>

                <div class="mb-2">
                    <label class="form-label">Tamaño de las imágenes</label>
                    <select class="form-select" name="questions[${i}][image_size]">
                        <option value="small">Pequeña</option>
                        <option value="medium" selected>Mediana</option>
                        <option value="large">Grande</option>
                    </select>
                </div>

                <div class="mb-2">
                    <label class="form-label">Fotos al lado de la pregunta</label>
                    <div class="d-flex align-items-center gap-2">
                        <span class="image-controls" data-question-index="${i}" hidden>
                            <button type="button" class="option-pill add-question-images-btn" data-question-index="${i}">Añadir imagen</button>
                        </span>
                        <small class="text-muted">Puedes subir una o varias fotos.</small>
                    </div>
                    <div class="question-image-controls mt-2" data-question-index="${i}" hidden>
                        <input type="file" class="form-control question-image-input" accept="image/*" multiple name="questions[new_${i}][question_images][]">
                        <div class="image-previews mt-2"></div>
                        <div class="mt-1"><small class="text-success question-image-status" data-question-index="${i}" hidden>Foto(s) subida(s)</small></div>
                    </div>
                </div>

                <div class="mb-2" data-option-images="${i}" hidden>
                    <label class="form-label">Imágenes por opción</label>
                    <small class="text-muted d-block">Cada opción ya trae su propio botón “Poner imagen”.</small>
                </div>
            </div>
        `);

        const select = box.querySelector(`[data-question-index="${i}"]`);
        const allowMultiple = box.querySelector(`#allow_multiple_${i}`);
        const maxSelectionsWrap = box.querySelector(`[data-max-selections-wrap="${i}"]`);

        select.addEventListener('change', function () {
            setOptionVisibility(i, this.value === 'multiple_choice');
        });

        allowMultiple.addEventListener('change', function () {
            maxSelectionsWrap.hidden = !this.checked;
        });

        const addOptionBtn = box.querySelector(`[data-options-editor="${i}"] .add-option`);
        if (addOptionBtn) addOptionBtn.addEventListener('click', () => addOption(i));

        box.querySelectorAll(`[data-options-editor="${i}"] .remove-option`).forEach((button) => {
            button.addEventListener('click', () => button.closest('.option-row').remove());
        });
    }

    document.getElementById('add').addEventListener('click', addQuestion);
    addQuestion();

    // Delegated handlers as fallback for dynamically added elements
    document.addEventListener('click', (ev) => {
        const t = ev.target;
        if (!t || !t.classList) return;
        if (t.classList.contains('add-option')) {
            const q = t.closest('.options-editor')?.dataset.optionsEditor;
            if (q) addOption(q);
        }
        if (t.classList.contains('remove-option')) {
            t.closest('.option-row')?.remove();
        }
    });

    // Show small indicator when question images are selected
    document.addEventListener('change', (e) => {
        const t = e.target;
        if (t.classList.contains('think-image-toggle')) {
            const q = t.dataset.questionIndex;
            document.querySelectorAll(`.image-controls[data-question-index="${q}"]`).forEach(n => n.hidden = !t.checked);
            return;
        }
        if (t.classList.contains('question-image-input')) {
            const input = t;
            const qIndexMatch = input.name.match(/questions\[(.*?)\]\[question_images\]/);
            if (!qIndexMatch) return;
            const idx = qIndexMatch[1];
            const status = document.querySelector(`.question-image-status[data-question-index="${idx}"]`);
            if (status) {
                status.hidden = input.files.length === 0;
                status.textContent = input.files.length ? `Foto(s) subida(s): ${input.files.length}` : 'Foto(s) subida(s)';
            }
        }

        if (t.classList.contains('option-file-input')) {
            const m = t.name.match(/questions\[(.*?)\]\[option_images\]\[(\d+)\]/);
            if (!m) return;
            const q = m[1];
            const opt = m[2];
            const status = document.querySelector(`.option-image-status[data-question-index="${q}"][data-option-index="${opt}"]`);
            if (status) {
                status.hidden = t.files.length === 0;
                status.textContent = t.files.length ? `Foto subida` : 'Foto subida';
            }
        }
    });

    // Prevent submission if total selected files exceed configured soft-limit
    document.getElementById('survey-form').addEventListener('submit', function (ev) {
        const MAX_BYTES = 20 * 1024 * 1024; // 20MB client-side soft limit (match MAX_SURVEY_UPLOAD_BYTES)
        let total = 0;
        document.querySelectorAll('input[type=file]').forEach((inp) => {
            for (let i = 0; i < inp.files.length; i++) total += inp.files[i].size;
        });
        if (total > MAX_BYTES) {
            ev.preventDefault();
            alert('El total de archivos seleccionados supera 20MB. Reduce el número o tamaño de imágenes, o adjusta el límite en el servidor.');
        }
    });

    // Toggle controls and show previews for newly created question/option inputs
    document.addEventListener('click', (e) => {
        if (e.target.classList.contains('add-question-images-btn')) {
            const q = e.target.dataset.questionIndex;
            const ctrl = document.querySelector(`.question-image-controls[data-question-index="${q}"]`);
            if (ctrl) ctrl.hidden = !ctrl.hidden;
        }
        if (e.target.classList.contains('add-image-btn')) {
            const q = e.target.dataset.questionIndex;
            const opt = e.target.dataset.optionIndex;
            const container = document.querySelector(`.option-image-container[data-question-index="${q}"][data-option-index="${opt}"]`);
            if (container) container.hidden = !container.hidden;
        }
    });

    document.addEventListener('change', (e) => {
        const t = e.target;
        if (t.classList.contains('question-image-input')) {
            const m = t.name.match(/questions\[(.*?)\]\[question_images\]/);
            if (!m) return;
            const q = m[1];
            const previews = document.querySelector(`.question-image-controls[data-question-index="${q}"] .image-previews`);
            if (!previews) return;
            previews.innerHTML = '';
            for (let i = 0; i < t.files.length; i++) {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(t.files[i]);
                img.className = 'img-preview';
                previews.appendChild(img);
            }
            // async upload files and add hidden inputs with returned URLs
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            // remove previous hidden url inputs for this question
            document.querySelectorAll(`input[name^="questions[${q}][question_images_urls]"]`).forEach(n => n.remove());
            (async () => {
                for (let i = 0; i < t.files.length; i++) {
                    const fd = new FormData(); fd.append('image', t.files[i]);
                    const res = await fetch('{{ route('admin.upload_image') }}', { method: 'POST', body: fd, headers: { 'X-CSRF-TOKEN': token } });
                    if (!res.ok) continue;
                    const json = await res.json();
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = `questions[${q}][question_images_urls][]`;
                    input.value = json.url;
                    document.getElementById('survey-form').appendChild(input);
                }
            })();
        }
        if (t.classList.contains('option-file-input')) {
            const m = t.name.match(/questions\[(.*?)\]\[option_images\]\[(\d+)\]/);
            if (!m) return;
            const q = m[1];
            const opt = m[2];
            const previews = document.querySelector(`.option-image-container[data-question-index="${q}"][data-option-index="${opt}"] .image-previews`);
            if (!previews) return;
            previews.innerHTML = '';
            if (t.files.length) {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(t.files[0]);
                img.className = 'img-preview';
                previews.appendChild(img);
            }
            // async upload single option image and add hidden input preserving index
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            (async () => {
                const fd = new FormData(); fd.append('image', t.files[0]);
                const res = await fetch('{{ route('admin.upload_image') }}', { method: 'POST', body: fd, headers: { 'X-CSRF-TOKEN': token } });
                if (!res.ok) return;
                const json = await res.json();
                // remove previous hidden input for this option index
                document.querySelectorAll(`input[name="questions[${q}][option_images_urls][${opt}]"]`).forEach(n=>n.remove());
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `questions[${q}][option_images_urls][${opt}]`;
                input.value = json.url;
                document.getElementById('survey-form').appendChild(input);
            })();
        }
    });
</script>
@endpush
