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
@push('scripts')
<script>
    let n = 0;
    const box = document.getElementById('questions');

    function toggleOptionImages(select) {
        const questionIndex = select.dataset.questionIndex;
        const optionImages = document.querySelector(`[data-option-images="${questionIndex}"]`);
        const optionsField = document.querySelector(`[data-options-field="${questionIndex}"]`);
        const shouldShow = select.value === 'multiple_choice';
        optionImages.hidden = !shouldShow;
        optionsField.hidden = !shouldShow;
    }

    function add() {
        const i = n++;
        box.insertAdjacentHTML('beforeend', `
            <div class="border rounded p-3 mb-3 question-card">
                <button type="button" class="btn-close float-end" onclick="this.parentElement.remove()"></button>
                <label class="form-label">Pregunta</label>
                <input class="form-control mb-2" name="questions[${i}][text]" required>

                <label class="form-label">Tipo</label>
                <select class="form-select mb-2 question-type" data-question-index="${i}" name="questions[${i}][type]">
                    <option value="text">Respuesta corta</option>
                    <option value="paragraph">Párrafo</option>
                    <option value="multiple_choice">Opción múltiple</option>
                    <option value="scale">Escala (1-5)</option>
                </select>

                <div class="mb-2" data-options-field="${i}" hidden>
                    <label class="form-label">Opciones separadas por coma</label>
                    <input class="form-control" name="questions[${i}][options]" placeholder="Ej.: Sí, No, Talvez">
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
            toggleOptionImages(this);
        });
    }

    document.getElementById('add').addEventListener('click', add);
    add();
</script>
@endpush
