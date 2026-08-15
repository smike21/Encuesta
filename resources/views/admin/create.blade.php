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

            <div class="border rounded p-3 mb-4">
                <button type="button" class="btn btn-outline-primary w-100" id="toggle-customization">Mostrar personalización</button>
                <div id="customization-panel" hidden>
                    <h2 class="h4 mt-3">Personalización global</h2>

                    <label class="form-label">Tipo de letra</label>
                    <select class="form-select mb-3" name="font_family">
                        <option value="Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial">Inter / System</option>
                        <option value="Arial, Helvetica, sans-serif">Arial</option>
                        <option value="Helvetica, Arial, sans-serif">Helvetica</option>
                        <option value="Verdana, Geneva, sans-serif">Verdana</option>
                        <option value="'Times New Roman', Times, serif">Times New Roman</option>
                        <option value="Georgia, 'Times New Roman', Times, serif">Georgia</option>
                        <option value="'Roboto', sans-serif">Roboto</option>
                        <option value="'Montserrat', sans-serif">Montserrat</option>
                    </select>

                    <label class="form-label">Tamaño base del texto</label>
                    <select class="form-select mb-3" name="font_size">
                        <option value="14px">Pequeño</option>
                        <option value="16px" selected>Normal</option>
                        <option value="18px">Grande</option>
                        <option value="20px">Muy grande</option>
                    </select>

                    <label class="form-label">Texto del botón</label>
                    <input class="form-control mb-3" name="button_text" placeholder="Enviar respuestas">

                    <div class="row g-2">
                        <div class="col-md-3"><label class="form-label">Color principal</label><input class="form-control" type="color" name="primary_color" value="#1e5bb0"></div>
                        <div class="col-md-3"><label class="form-label">Fondo de la página</label><input class="form-control" type="color" name="background_color" value="#eaf3ff"></div>
                        <div class="col-md-3"><label class="form-label">Fondo del contenedor</label><input class="form-control" type="color" name="container_background_color" value="#eaf3ff"></div>
                        <div class="col-md-3"><label class="form-label">Color borde contenedor</label><input class="form-control" type="color" name="container_border_color" value="#1e5bb0"></div>
                    </div>
                    <div class="row g-2 mt-2">
                        <div class="col-md-4"><label class="form-label">Color de texto</label><input class="form-control" type="color" name="text_color" value="#3d2516"></div>
                    </div>

                    <div class="mt-3">
                        <div class="small text-uppercase fw-bold text-muted">Vista previa</div>
                        <div id="customization-preview" class="border rounded p-3 mt-2 position-relative" style="background:#eaf3ff; color:#102a44;">
                            <div class="card mb-3" style="border-color:#1e5bb0; background:#eaf3ff; color:#102a44;">
                                <div class="card-body">
                                    <h5 style="color:#1e5bb0">Bienvenido</h5>
                                    <p class="mb-0">Tu opinión ayuda mucho.</p>
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="d-flex justify-content-between small mb-1">
                                    <span>Progreso</span>
                                    <span>1 / 3</span>
                                </div>
                                <div class="progress-preview" style="height:8px; background:#dbe9ff; border-radius:999px; overflow:hidden;">
                                    <div style="height:100%; width:33%; background:#1e5bb0;"></div>
                                </div>
                            </div>
                            <button type="button" class="btn btn-primary" style="background:#1e5bb0; border-color:#1e5bb0;">Enviar respuestas</button>
                        </div>
                    </div>

                    <div class="form-check form-switch mt-3">
                        <input class="form-check-input" type="checkbox" value="1" name="show_title" id="show_title" checked>
                        <label class="form-check-label" for="show_title">Mostrar título</label>
                    </div>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" value="1" name="show_description" id="show_description" checked>
                        <label class="form-check-label" for="show_description">Mostrar descripción</label>
                    </div>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" value="1" name="show_progress" id="show_progress" checked>
                        <label class="form-check-label" for="show_progress">Mostrar progreso</label>
                    </div>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" value="1" name="show_submit_button" id="show_submit_button" checked>
                        <label class="form-check-label" for="show_submit_button">Mostrar botón de envío</label>
                    </div>
                    <div class="mt-3">
                        <button type="button" id="apply-preview-btn" class="btn btn-secondary btn-sm">Actualizar vista previa</button>
                    </div>
                </div>
            </div>

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
    .question-card .question-top .title-chip { display:inline-flex; align-items:center; gap:.45rem; padding:.35rem .7rem; border-radius:999px; background:#eaf3ff; color:#1e3f72; font-size:.78rem; font-weight:800; }
    .options-editor { border:1px solid #c5d8f2; border-radius:16px; padding:1rem; background:#f5f8ff; margin-bottom:1rem; }
    .options-list { display:grid; gap:.65rem; }
    .option-row { display:grid; grid-template-columns:1fr auto; gap:.6rem; align-items:start; }
    .option-row input { width:100%; }
    .option-pill { border:0; background:#fff; border:1px solid #c5d8f2; border-radius:10px; padding:.55rem .8rem; display:inline-flex; align-items:center; gap:.45rem; font:inherit; color:#1e3f72; cursor:pointer; }
    .option-pill--danger { color:#9a2020; }
    .option-media-wrap { display:flex; align-items:center; gap:.6rem; margin-top:.45rem; }
    .option-file-input { display:none; }
    .option-file-label { min-width:140px; cursor:pointer; }
    .img-preview { width:48px; height:48px; object-fit:cover; border-radius:8px; margin-right:.5rem; border:1px solid #d6e4f0; }
    .image-previews { display:flex; align-items:center; margin-top:.5rem; }
    .image-controls { display:inline-flex; align-items:center; gap:.4rem; }
</style>
@endpush
@push('scripts')
<script>
    let n = 0;
    let uploadsInProgress = 0;
    const box = document.getElementById('questions');

    function setOptionVisibility(questionIndex, isMultiple) {
        const optionsEditor = document.querySelector(`[data-options-editor="${questionIndex}"]`);
        const optionImages = document.querySelector(`[data-option-images="${questionIndex}"]`);
        const maxSelectionsWrap = document.querySelector(`[data-max-selections-wrap="${questionIndex}"]`);
        if (optionsEditor) optionsEditor.hidden = !isMultiple;
        if (optionsEditor) optionsEditor.querySelectorAll('input, select, textarea, button').forEach((field) => field.disabled = !isMultiple);
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
                                <span>🖼️ Añadir imagen</span>
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
                        <button type="button" class="btn-close float-end" onclick="this.closest('.question-card').remove()"></button>
                    </div>
                </div>

                <label class="form-label">Texto de la pregunta</label>
                <textarea class="form-control mb-2" rows="3" name="questions[${i}][text]" required></textarea>

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
                                                <span>🖼️ Añadir imagen</span>
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
                                                <span>🖼️ Añadir imagen</span>
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
                        <small class="text-muted">Límite total de subida 20MB, máximo 2MB por imagen.</small>
                    </div>
                    <div class="question-image-controls mt-2" data-question-index="${i}" hidden>
                        <input type="file" class="form-control question-image-input" accept="image/*" multiple name="questions[new_${i}][question_images][]">
                        <div class="image-previews mt-2"></div>
                        <div class="mt-1"><small class="text-success question-image-status" data-question-index="${i}" hidden>Foto(s) subida(s)</small></div>
                    </div>
                </div>

                <div class="mb-2" data-option-images="${i}" hidden>
                    <label class="form-label">Imágenes por opción</label>
                    <small class="text-muted d-block">Cada opción ya trae su propio botón “Añadir imagen”.</small>
                </div>
            </div>
        `);

        const select = box.querySelector(`.question-type[data-question-index="${i}"]`);
        const allowMultiple = box.querySelector(`#allow_multiple_${i}`);
        const maxSelectionsWrap = box.querySelector(`[data-max-selections-wrap="${i}"]`);

        if (select) {
            select.addEventListener('change', function () {
                setOptionVisibility(i, this.value === 'multiple_choice');
            });
            // initialize visibility based on default value
            setOptionVisibility(i, select.value === 'multiple_choice');
        }

        if (allowMultiple) {
            allowMultiple.addEventListener('change', function () {
                if (maxSelectionsWrap) maxSelectionsWrap.hidden = !this.checked;
            });
        }

        const addOptionBtn = box.querySelector(`[data-options-editor="${i}"] .add-option`);
        if (addOptionBtn) addOptionBtn.addEventListener('click', () => addOption(i));

        box.querySelectorAll(`[data-options-editor="${i}"] .remove-option`).forEach((button) => {
            button.addEventListener('click', () => button.closest('.option-row').remove());
        });
    }

    function updateCustomizationPreview() {
        const form = document.getElementById('survey-form');
        const preview = document.getElementById('customization-preview');
        if (!form || !preview) return;

        const title = form.querySelector('[name="title"]')?.value || 'Encuesta';
        const description = form.querySelector('[name="description"]')?.value || '';
        const buttonText = form.querySelector('[name="button_text"]')?.value || 'Enviar respuestas';
        const primaryColor = form.querySelector('[name="primary_color"]')?.value || '#b95712';
        const backgroundColor = form.querySelector('[name="background_color"]')?.value || '#fff7ef';
        const containerBg = form.querySelector('[name="container_background_color"]')?.value || backgroundColor;
        const containerBorder = form.querySelector('[name="container_border_color"]')?.value || primaryColor;
        const textColor = form.querySelector('[name="text_color"]')?.value || '#3d2516';
        const fontFamily = form.querySelector('[name="font_family"]')?.value || '';
        const fontSize = form.querySelector('[name="font_size"]')?.value || '';
        const showTitle = !!form.querySelector('[name="show_title"]')?.checked;
        const showDescription = !!form.querySelector('[name="show_description"]')?.checked;
        const showProgress = !!form.querySelector('[name="show_progress"]')?.checked;
        const showSubmit = !!form.querySelector('[name="show_submit_button"]')?.checked;

        preview.style.backgroundColor = backgroundColor;
        preview.style.color = textColor;
        if (fontFamily) preview.style.fontFamily = fontFamily;
        if (fontSize) preview.style.fontSize = fontSize;
        const card = preview.querySelector('.card');
        if (card) {
            card.style.backgroundColor = containerBg;
            card.style.color = textColor;
            card.style.borderColor = containerBorder;
            const h5 = card.querySelector('h5');
            if (h5) {
                h5.style.color = primaryColor;
                h5.textContent = showTitle ? (title || 'Bienvenido') : '';
            }
            const p = card.querySelector('p');
            if (p) {
                p.textContent = description;
                p.style.display = showDescription ? '' : 'none';
            }
        }

        const smallProgress = preview.querySelector('.mb-3 .small.mb-1');
        if (smallProgress) smallProgress.style.display = showProgress ? '' : 'none';
        const progressBarWrap = preview.querySelector('.mb-3 .progress-preview');
        if (progressBarWrap) progressBarWrap.style.display = showProgress ? '' : 'none';

        const btn = preview.querySelector('button');
        if (btn) {
            btn.style.display = showSubmit ? '' : 'none';
            btn.textContent = buttonText;
            btn.style.backgroundColor = primaryColor;
            btn.style.borderColor = primaryColor;
        }
    }

    document.getElementById('toggle-customization')?.addEventListener('click', function () {
        const panel = document.getElementById('customization-panel');
        const hidden = panel.hidden;
        panel.hidden = !hidden;
        this.textContent = hidden ? 'Ocultar personalización' : 'Mostrar personalización';
        if (hidden) updateCustomizationPreview();
    });

    const previewFieldNames = [
        'title', 'description', 'button_text',
        'primary_color', 'background_color', 'container_background_color',
        'container_border_color', 'text_color',
        'font_family', 'font_size',
        'show_title', 'show_description', 'show_progress', 'show_submit_button'
    ];
    const surveyFormEl = document.getElementById('survey-form');
    if (surveyFormEl) {
        previewFieldNames.forEach((fieldName) => {
            const field = surveyFormEl.querySelector(`[name="${fieldName}"]`);
            if (!field) return;
            field.addEventListener('input', updateCustomizationPreview);
            field.addEventListener('change', updateCustomizationPreview);
        });
    }

    document.addEventListener('click', (e) => {
        if (e.target && e.target.id === 'apply-preview-btn') {
            updateCustomizationPreview();
        }
    });

    updateCustomizationPreview();
    document.getElementById('add').addEventListener('click', addQuestion);
    addQuestion();

    // Handle dynamic select/change behavior and image selection states
    document.addEventListener('change', (e) => {
        const t = e.target;
        if (t.classList.contains('question-type')) {
            setOptionVisibility(t.dataset.questionIndex, t.value === 'multiple_choice');
            return;
        }
        if (t.classList.contains('allow-multiple')) {
            const q = t.closest('.options-editor')?.dataset.optionsEditor;
            if (q) {
                const maxWrap = document.querySelector(`[data-max-selections-wrap="${q}"]`);
                if (maxWrap) maxWrap.hidden = !t.checked;
            }
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
            return;
        }

        if (typeof uploadsInProgress !== 'undefined' && uploadsInProgress > 0) {
            ev.preventDefault();
            alert('Aún se están subiendo imágenes. Espera unos segundos y vuelve a intentarlo.');
            return;
        }

        document.querySelectorAll('input[type=file]').forEach((inp) => {
            if (inp.classList.contains('question-image-input')) {
                const q = inp.name.match(/questions\[(.*?)\]\[question_images\]/)?.[1];
                if (q && document.querySelector(`input[name="questions[${q}][question_images_urls][]"]`)) {
                    inp.disabled = true;
                }
            }
            if (inp.classList.contains('option-file-input')) {
                const m = inp.name.match(/questions\[(.*?)\]\[option_images\]\[(\d+)\]/);
                if (m) {
                    const q = m[1];
                    const opt = m[2];
                    if (document.querySelector(`input[name="questions[${q}][option_images_urls][${opt}]"]`)) {
                        inp.disabled = true;
                    }
                }
            }
        });
    });

    // Toggle controls and show previews for newly created question/option inputs
    document.addEventListener('click', (e) => {
        const questionButton = e.target.closest('.add-question-images-btn');
        if (questionButton) {
            const q = questionButton.dataset.questionIndex;
            const ctrl = document.querySelector(`.question-image-controls[data-question-index="${q}"]`);
            if (ctrl) ctrl.hidden = !ctrl.hidden;
            return;
        }

        const optionButton = e.target.closest('.add-image-btn');
        if (optionButton) {
            const q = optionButton.dataset.questionIndex;
            const opt = optionButton.dataset.optionIndex;
            const container = document.querySelector(`.option-image-container[data-question-index="${q}"][data-option-index="${opt}"]`);
            if (container) {
                container.hidden = !container.hidden;
                if (!container.hidden) {
                    const input = container.querySelector('.option-file-input');
                    if (input) input.click();
                }
            }
            return;
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
                uploadsInProgress++;
                try {
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
                } finally { uploadsInProgress--; }
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
                uploadsInProgress++;
                try {
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
                } finally { uploadsInProgress--; }
            })();
        }
    });
</script>
@endpush
