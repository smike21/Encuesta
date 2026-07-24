@extends('layouts.app')
@section('title','Crear encuesta')
@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <h1>Crear encuesta</h1>
        <form class="card p-4" method="post" action="{{ route('admin.store') }}" enctype="multipart/form-data">
            @csrf
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
    .question-card {
        background: #fffdfb;
    }

    .question-card .question-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .question-card .question-top .title-chip {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        padding: .35rem .7rem;
        border-radius: 999px;
        background: #fff1e4;
        color: #8e3d08;
        font-size: .78rem;
        font-weight: 800;
    }

    .options-editor {
        border: 1px solid #ead8c7;
        border-radius: 16px;
        padding: 1rem;
        background: #fff8f2;
        margin-bottom: 1rem;
    }

    .options-list {
        display: grid;
        gap: .6rem;
    }

    .option-row {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: .6rem;
        align-items: center;
    }

    .option-row input {
        width: 100%;
    }

    .option-pill {
        border: 0;
        background: #fff;
        border: 1px solid #dfc8b6;
        border-radius: 10px;
        padding: .55rem .8rem;
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        font: inherit;
        color: #8e3d08;
        cursor: pointer;
    }

    .option-pill--danger {
        color: #9a2020;
    }
</style>
@endpush
@push('scripts')
<script>
    let n = 0;
    const box = document.getElementById('questions');

    function setOptionVisibility(questionIndex, isMultiple) {
        const optionsEditor = document.querySelector(`[data-options-editor="${questionIndex}"]`);
        const optionImages = document.querySelector(`[data-option-images="${questionIndex}"]`);
        const shouldShow = isMultiple;

        if (optionsEditor) optionsEditor.hidden = !shouldShow;
        if (optionImages) optionImages.hidden = !shouldShow;
    }

    function addOption(questionIndex) {
        const list = document.querySelector(`[data-options-list="${questionIndex}"]`);
        const row = document.createElement('div');
        row.className = 'option-row';
        row.innerHTML = `
            <input class="form-control" type="text" name="questions[${questionIndex}][options][]" placeholder="Escribe una opción" required>
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
                <input class="form-control mb-2" name="questions[${i}][text]" required>

                <label class="form-label">Tipo</label>
                <select class="form-select mb-3 question-type" data-question-index="${i}" name="questions[${i}][type]">
                    <option value="text">Respuesta corta</option>
                    <option value="paragraph">Párrafo</option>
                    <option value="multiple_choice">Opción múltiple</option>
                    <option value="scale">Escala (1-5)</option>
                </select>

                <div class="options-editor" data-options-editor="${i}" hidden>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label class="form-label mb-0">Opciones</label>
                        <button type="button" class="option-pill add-option">Agregar opción</button>
                    </div>
                    <div class="options-list" data-options-list="${i}">
                        <div class="option-row">
                            <input class="form-control" type="text" name="questions[${i}][options][]" placeholder="Escribe una opción" required>
                            <button type="button" class="option-pill option-pill--danger remove-option">Eliminar</button>
                        </div>
                        <div class="option-row">
                            <input class="form-control" type="text" name="questions[${i}][options][]" placeholder="Escribe una opción" required>
                            <button type="button" class="option-pill option-pill--danger remove-option">Eliminar</button>
                        </div>
                    </div>
                </div>

                <div class="mb-2">
                    <label class="form-label">Fotos al lado de la pregunta</label>
                    <input type="file" class="form-control" accept="image/*" multiple name="questions[${i}][question_images][]">
                    <small class="text-muted">Puedes subir una o varias fotos.</small>
                </div>

                <div class="mb-2" data-option-images="${i}" hidden>
                    <label class="form-label">Imágenes por opción</label>
                    <input type="file" class="form-control" accept="image/*" multiple name="questions[${i}][option_images][]">
                    <small class="text-muted">Se emparejan en el mismo orden que las opciones.</small>
                </div>
            </div>
        `);

        const select = box.querySelector(`[data-question-index="${i}"]`);
        select.addEventListener('change', function () {
            setOptionVisibility(i, this.value === 'multiple_choice');
        });

        const addOptionBtn = box.querySelector(`[data-options-editor="${i}"] .add-option`);
        addOptionBtn.addEventListener('click', () => addOption(i));

        box.querySelectorAll(`[data-options-editor="${i}"] .remove-option`).forEach((button) => {
            button.addEventListener('click', () => button.closest('.option-row').remove());
        });
    }

    document.getElementById('add').addEventListener('click', addQuestion);
    addQuestion();
</script>
@endpush
