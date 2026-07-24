@extends('layouts.app')
@section('title','Editar encuesta')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <h1>Editar encuesta</h1>
        <form id="survey-form" class="card p-4" method="post" action="{{ route('admin.update', $survey) }}" enctype="multipart/form-data">
            @csrf
            @method('PUT')

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
            <input class="form-control mb-3" name="title" value="{{ old('title', $survey->title) }}" required>

            <label class="form-label">Descripción</label>
            <textarea class="form-control mb-3" name="description">{{ old('description', $survey->description) }}</textarea>

            <div class="form-check mb-4">
                <input class="form-check-input" type="checkbox" value="1" name="collect_location" id="location" {{ old('collect_location', $survey->collect_location) ? 'checked' : '' }}>
                <label class="form-check-label" for="location">Solicitar ubicación al responder</label>
            </div>

            <h2 class="h4">Preguntas</h2>
            <div id="questions">
                @foreach($survey->questions as $question)
                    <div class="border rounded p-3 mb-3 question-card">
                        <div class="question-top mb-3">
                            <span class="title-chip">Pregunta</span>
                            <button type="button" class="btn-close float-end" onclick="this.parentElement.parentElement.remove()"></button>
                        </div>

                        <input type="hidden" name="questions[{{ $question->id }}][id]" value="{{ $question->id }}">

                        <label class="form-label">Texto de la pregunta</label>
                        <input class="form-control mb-2" name="questions[{{ $question->id }}][text]" value="{{ old("questions.{$question->id}.text", $question->text) }}" required>

                        <label class="form-label">Tipo</label>
                        <select class="form-select mb-3 question-type" data-question-index="{{ $question->id }}" name="questions[{{ $question->id }}][type]">
                            <option value="text" {{ $question->type === 'text' ? 'selected' : '' }}>Respuesta corta</option>
                            <option value="paragraph" {{ $question->type === 'paragraph' ? 'selected' : '' }}>Párrafo</option>
                            <option value="multiple_choice" {{ $question->type === 'multiple_choice' ? 'selected' : '' }}>Opción múltiple</option>
                            <option value="scale" {{ $question->type === 'scale' ? 'selected' : '' }}>Escala (1-5)</option>
                        </select>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" value="1" name="questions[{{ $question->id }}][is_required]" id="required_{{ $question->id }}" {{ old("questions.{$question->id}.is_required", $question->is_required) ? 'checked' : '' }}>
                            <label class="form-check-label" for="required_{{ $question->id }}">Pregunta obligatoria</label>
                        </div>

                        <div class="options-editor" data-options-editor="{{ $question->id }}" {{ $question->type !== 'multiple_choice' ? 'hidden' : '' }}>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">Opciones</label>
                                <button type="button" class="option-pill add-option">Agregar opción</button>
                            </div>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input allow-multiple" type="checkbox" value="1" name="questions[{{ $question->id }}][allow_multiple]" id="allow_multiple_{{ $question->id }}" {{ old("questions.{$question->id}.allow_multiple", $question->allow_multiple) ? 'checked' : '' }}>
                                <label class="form-check-label" for="allow_multiple_{{ $question->id }}">Permitir varias opciones</label>
                            </div>

                            <div class="mb-3" data-max-selections-wrap="{{ $question->id }}" {{ !$question->allow_multiple ? 'hidden' : '' }}>
                                <label class="form-label">Número máximo de opciones permitidas</label>
                                <input class="form-control" type="number" min="1" name="questions[{{ $question->id }}][max_selections]" value="{{ old("questions.{$question->id}.max_selections", $question->max_selections ?? 1) }}" placeholder="Ej. 2">
                            </div>

                            <div class="options-list" data-options-list="{{ $question->id }}">
                                @if($question->type === 'multiple_choice' && !empty($question->options))
                                    @foreach($question->options as $index => $option)
                                        <div class="option-row">
                                            <div>
                                                <input class="form-control" type="text" name="questions[{{ $question->id }}][options][]" value="{{ old("questions.{$question->id}.options.{$index}", $option) }}" placeholder="Escribe una opción" required>
                                                <div class="d-flex align-items-center gap-2 mt-2">
                                                    <button type="button" class="option-pill add-image-btn" data-question-index="{{ $question->id }}" data-option-index="{{ $index }}">Añadir imagen</button>
                                                    <div class="option-image-container" data-question-index="{{ $question->id }}" data-option-index="{{ $index }}" {{ empty($question->option_images[$index] ?? null) ? 'hidden' : '' }}>
                                                        <div class="option-media-wrap">
                                                            <label class="option-pill option-file-label">
                                                                <span>🖼️ Cambiar imagen</span>
                                                                <input class="option-file-input" type="file" accept="image/*" name="questions[{{ $question->id }}][option_images][{{ $index }}]">
                                                            </label>
                                                            <div><small class="text-success option-image-status" data-question-index="{{ $question->id }}" data-option-index="{{ $index }}" hidden>Foto subida</small></div>
                                                        </div>
                                                        <div class="image-previews">
                                                            @if(!empty($question->option_images[$index] ?? null))
                                                                <div class="d-flex align-items-center" style="gap:.5rem">
                                                                    <img src="{{ $question->option_images[$index] }}" class="img-preview" alt="preview">
                                                                    <label style="display:flex;align-items:center;gap:.4rem;cursor:pointer">
                                                                        <input type="checkbox" name="questions[{{ $question->id }}][remove_option_images][{{ $index }}]" value="1" class="remove-image-checkbox"> Eliminar
                                                                    </label>
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="button" class="option-pill option-pill--danger remove-option">Eliminar</button>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="option-row">
                                        <div>
                                            <input class="form-control" type="text" name="questions[{{ $question->id }}][options][]" placeholder="Escribe una opción" required>
                                            <div class="d-flex align-items-center gap-2 mt-2">
                                                <button type="button" class="option-pill add-image-btn" data-question-index="{{ $question->id }}" data-option-index="0">Añadir imagen</button>
                                                <div class="option-image-container" data-question-index="{{ $question->id }}" data-option-index="0" hidden>
                                                    <div class="option-media-wrap">
                                                        <label class="option-pill option-file-label">
                                                            <span>🖼️ Poner imagen</span>
                                                            <input class="option-file-input" type="file" accept="image/*" name="questions[{{ $question->id }}][option_images][0]">
                                                        </label>
                                                        <div><small class="text-success option-image-status" data-question-index="{{ $question->id }}" data-option-index="0" hidden>Foto subida</small></div>
                                                    </div>
                                                    <div class="image-previews"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="option-pill option-pill--danger remove-option">Eliminar</button>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Tamaño de las imágenes</label>
                            <select class="form-select" name="questions[{{ $question->id }}][image_size]">
                                <option value="small" {{ old("questions.{$question->id}.image_size", $question->image_size ?? 'medium') === 'small' ? 'selected' : '' }}>Pequeña</option>
                                <option value="medium" {{ old("questions.{$question->id}.image_size", $question->image_size ?? 'medium') === 'medium' ? 'selected' : '' }}>Mediana</option>
                                <option value="large" {{ old("questions.{$question->id}.image_size", $question->image_size ?? 'medium') === 'large' ? 'selected' : '' }}>Grande</option>
                            </select>
                        </div>

                        <div class="mb-2">
                            <label class="form-label">Fotos al lado de la pregunta</label>
                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="option-pill add-question-images-btn" data-question-index="{{ $question->id }}">Añadir imagen</button>
                                <small class="text-muted">Puedes subir una o varias fotos nuevas; si dejas esto vacío, se mantienen las existentes.</small>
                            </div>
                            <div class="question-image-controls mt-2" data-question-index="{{ $question->id }}" {{ empty($question->question_images) ? 'hidden' : '' }}>
                                <input type="file" class="form-control question-image-input" accept="image/*" multiple name="questions[{{ $question->id }}][question_images][]">
                                <div class="image-previews mt-2">
                                    @if(!empty($question->question_images))
                                        @foreach($question->question_images as $idx => $img)
                                            <div class="d-flex align-items-center" style="gap:.5rem">
                                                <img src="{{ $img }}" class="img-preview" alt="preview">
                                                <label style="display:flex;align-items:center;gap:.4rem;cursor:pointer">
                                                    <input type="checkbox" name="questions[{{ $question->id }}][remove_question_images][{{ $idx }}]" value="1" class="remove-image-checkbox"> Eliminar
                                                </label>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                                <div class="mt-1"><small class="text-success question-image-status" data-question-index="{{ $question->id }}" hidden>Foto(s) subida(s)</small></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <button type="button" class="btn btn-outline-primary my-3" id="add">Agregar pregunta</button>
            <button class="btn btn-primary w-100">Guardar cambios</button>
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
    .img-preview { width:72px; height:72px; object-fit:cover; border-radius:8px; margin-right:.5rem; border:1px solid #e6d7c7; }
    .image-previews { display:flex; align-items:center; margin-top:.5rem; }
    .to-remove { opacity:.45; filter:grayscale(80%); }
</style>
@endpush
@push('scripts')
<script>
    let n = 0;
    const box = document.getElementById('questions');

    function setOptionVisibility(questionIndex, isMultiple) {
        const optionsEditor = document.querySelector(`[data-options-editor="${questionIndex}"]`);
        const maxSelectionsWrap = document.querySelector(`[data-max-selections-wrap="${questionIndex}"]`);
        if (optionsEditor) optionsEditor.hidden = !isMultiple;
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
                <div class="option-media-wrap">
                    <label class="option-pill option-file-label">
                        <span>🖼️ Poner imagen</span>
                        <input class="option-file-input" type="file" accept="image/*" name="questions[${questionIndex}][option_images][${optionIndex}]">
                    </label>
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
                    <button type="button" class="btn-close float-end" onclick="this.parentElement.parentElement.remove()"></button>
                </div>

                <label class="form-label">Texto de la pregunta</label>
                <input class="form-control mb-2" name="questions[new_${i}][text]" required>

                <label class="form-label">Tipo</label>
                <select class="form-select mb-3 question-type" data-question-index="new_${i}" name="questions[new_${i}][type]">
                    <option value="text">Respuesta corta</option>
                    <option value="paragraph">Párrafo</option>
                    <option value="multiple_choice">Opción múltiple</option>
                    <option value="scale">Escala (1-5)</option>
                </select>

                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" value="1" name="questions[new_${i}][is_required]" id="required_new_${i}" checked>
                    <label class="form-check-label" for="required_new_${i}">Pregunta obligatoria</label>
                </div>

                <div class="options-editor" data-options-editor="new_${i}" hidden>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label mb-0">Opciones</label>
                        <button type="button" class="option-pill add-option">Agregar opción</button>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input allow-multiple" type="checkbox" value="1" name="questions[new_${i}][allow_multiple]" id="allow_multiple_new_${i}">
                        <label class="form-check-label" for="allow_multiple_new_${i}">Permitir varias opciones</label>
                    </div>

                    <div class="mb-3" data-max-selections-wrap="new_${i}" hidden>
                        <label class="form-label">Número máximo de opciones permitidas</label>
                        <input class="form-control" type="number" min="1" name="questions[new_${i}][max_selections]" value="1" placeholder="Ej. 2">
                    </div>

                    <div class="options-list" data-options-list="new_${i}">
                        <div class="option-row">
                            <div>
                                <input class="form-control" type="text" name="questions[new_${i}][options][]" placeholder="Escribe una opción" required>
                                <div class="option-media-wrap">
                                    <label class="option-pill option-file-label">
                                        <span>🖼️ Poner imagen</span>
                                        <input class="option-file-input" type="file" accept="image/*" name="questions[new_${i}][option_images][0]">
                                    </label>
                                </div>
                            </div>
                            <button type="button" class="option-pill option-pill--danger remove-option">Eliminar</button>
                        </div>
                    </div>
                </div>

                <div class="mb-2">
                    <label class="form-label">Tamaño de las imágenes</label>
                    <select class="form-select" name="questions[new_${i}][image_size]">
                        <option value="small">Pequeña</option>
                        <option value="medium" selected>Mediana</option>
                        <option value="large">Grande</option>
                    </select>
                </div>

                <div class="mb-2">
                    <label class="form-label">Fotos al lado de la pregunta</label>
                    <input type="file" class="form-control" accept="image/*" multiple name="questions[new_${i}][question_images][]">
                    <small class="text-muted">Puedes subir una o varias fotos.</small>
                </div>
            </div>
        `);

        const select = box.querySelector(`[data-question-index="new_${i}"]`);
        const allowMultiple = box.querySelector(`#allow_multiple_new_${i}`);
        const maxSelectionsWrap = box.querySelector(`[data-max-selections-wrap="new_${i}"]`);

        select.addEventListener('change', function () {
            setOptionVisibility(`new_${i}`, this.value === 'multiple_choice');
        });

        allowMultiple.addEventListener('change', function () {
            maxSelectionsWrap.hidden = !this.checked;
        });

        const addOptionBtn = box.querySelector(`[data-options-editor="new_${i}"] .add-option`);
        addOptionBtn.addEventListener('click', () => addOption(`new_${i}`));

        box.querySelectorAll(`[data-options-editor="new_${i}"] .remove-option`).forEach((button) => {
            button.addEventListener('click', () => button.closest('.option-row').remove());
        });
    }

    document.getElementById('add').addEventListener('click', addQuestion);

    box.querySelectorAll('.question-type').forEach((select) => {
        select.addEventListener('change', function () {
            setOptionVisibility(this.dataset.questionIndex, this.value === 'multiple_choice');
        });
    });

    box.querySelectorAll('.remove-option').forEach((button) => {
        button.addEventListener('click', () => button.closest('.option-row').remove());
    });

    box.querySelectorAll('.add-option').forEach((button) => {
        const questionIndex = button.closest('.options-editor').dataset.optionsEditor;
        button.addEventListener('click', () => addOption(questionIndex));
    });

    // Show indicator when question or option images are selected
    document.addEventListener('change', (e) => {
        const t = e.target;
        if (t.classList.contains('question-image-input')) {
            const m = t.name.match(/questions\[(.*?)\]\[question_images\]/);
            if (!m) return;
            const q = m[1];
            const status = document.querySelector(`.question-image-status[data-question-index="${q}"]`);
            if (status) {
                status.hidden = t.files.length === 0;
                status.textContent = t.files.length ? `Foto(s) subida(s): ${t.files.length}` : 'Foto(s) subida(s)';
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

        // Mark preview when a remove checkbox is toggled
        if (t.classList.contains('remove-image-checkbox')) {
            const img = t.closest('div').querySelector('img.img-preview');
            if (img) img.classList.toggle('to-remove', t.checked);
        }
    });

    // Client-side guard for large total upload size
    document.getElementById('survey-form').addEventListener('submit', function (ev) {
        const MAX_BYTES = 20 * 1024 * 1024; // 20MB
        let total = 0;
        document.querySelectorAll('input[type=file]').forEach((inp) => {
            for (let i = 0; i < inp.files.length; i++) total += inp.files[i].size;
        });
        if (total > MAX_BYTES) {
            ev.preventDefault();
            alert('El total de archivos seleccionados supera 20MB. Reduce el número o tamaño de imágenes, o adjusta el límite en el servidor.');
        }
    });

    // Toggle question image controls and option image containers, and show previews
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
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            (async () => {
                const fd = new FormData(); fd.append('image', t.files[0]);
                const res = await fetch('{{ route('admin.upload_image') }}', { method: 'POST', body: fd, headers: { 'X-CSRF-TOKEN': token } });
                if (!res.ok) return;
                const json = await res.json();
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
