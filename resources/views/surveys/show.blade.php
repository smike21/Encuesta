@extends('layouts.app')
@section('title',$survey->title)
@section('content')
<a href="{{ route('surveys.index') }}">← Volver</a>
<div class="card my-4">
    <div class="card-body">
        <h1>{{ $survey->title }}</h1>
        <p class="mb-0">{{ $survey->description }}</p>
    </div>
</div>

<form method="post" action="{{ route('surveys.submit',$survey) }}" style="background-color:{{ $survey->background_color ?: '#eef1f5' }}; color:{{ $survey->text_color ?: '#102a44' }}; font-family:{{ $survey->font_family ?: "Inter, system-ui, -apple-system, 'Segoe UI', Roboto, 'Helvetica Neue', Arial" }}; font-size:{{ $survey->font_size ?: '16px' }}; border-radius:24px; padding:1rem;">
    @csrf
    <input type="hidden" name="timezone" id="timezone">
    <input type="hidden" name="locale" id="locale">

    @if(($survey->show_title ?? true) && ($survey->welcome_title || $survey->welcome_text))
        <div class="card mb-4" style="border-color:{{ $survey->primary_color ?: '#1e5bb0' }}; background-color:{{ $survey->background_color ?: '#f6f8fb' }}; color:{{ $survey->text_color ?: '#102a44' }};">
            <div class="card-body">
                @if($survey->welcome_title)
                    <h1 style="color:{{ $survey->primary_color ?: '#1e5bb0' }};">{{ $survey->welcome_title }}</h1>
                @endif
                @if(($survey->show_description ?? true) && $survey->welcome_text)
                    <p class="mb-0 mt-2">{{ $survey->welcome_text }}</p>
                @endif
            </div>
        </div>
    @endif

    @if($survey->collect_location)
        <section class="card mb-3" style="background-color:{{ $survey->background_color ?: '#f4f6f8' }}; color:{{ $survey->text_color ?: '#102a44' }}; border-color:{{ $survey->primary_color ?: '#1e5bb0' }};">
            <div class="card-body">
                <h2 class="h5">Compartir ubicación (opcional)</h2>
                <button type="button" class="btn btn-outline-primary" id="locate">Obtener mi ubicación</button>
                <span id="location-status" class="ms-2"></span>
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">
            </div>
        </section>
    @endif

    @if(($survey->show_progress ?? true) && $survey->questions->count())
        <div class="mb-3">
            <div class="d-flex justify-content-between small mb-1">
                <span>Progreso</span>
                <span id="progress-count">0 / {{ $survey->questions->count() }}</span>
            </div>
            <div style="height:8px; background:#d7dce4; border-radius:999px; overflow:hidden;">
                <div id="progress-bar-fill" style="height:100%; width:0%; background:{{ $survey->primary_color ?: '#1e5bb0' }}; transition: width .4s ease;"></div>
            </div>
            <div id="progress-complete-msg" class="text-center mt-2" style="display:none; color:{{ $survey->primary_color ?: '#1e5bb0' }}; font-weight:600; opacity:0; transition: opacity .4s ease;">
                🎉 ¡Todo listo para enviar!
            </div>
        </div>
    @endif

    @foreach($survey->questions as $question)
        <section class="card mb-3" data-question style="border-color:{{ $survey->primary_color ?: '#1e5bb0' }}; background-color:{{ $survey->background_color ?: '#f4f6f8' }}; color:{{ $survey->text_color ?: '#102a44' }};">
            <div class="card-body">
                <label class="form-label fw-bold">{{ $question->text }}</label>

                @php
                    $imageSizeClass = match ($question->image_size ?? 'medium') {
                        'small' => 'question-media-item--small',
                        'large' => 'question-media-item--large',
                        default => 'question-media-item--medium',
                    };
                @endphp

                @if(!empty($question->question_images))
                    <div class="question-media-grid">
                        @foreach($question->question_images as $imagePath)
                            <img src="{{ str_starts_with($imagePath, 'data:') || str_starts_with($imagePath, 'http') || str_starts_with($imagePath, '/') ? $imagePath : Storage::disk('public')->url($imagePath) }}" alt="Imagen de pregunta" class="question-media-item {{ $imageSizeClass }}">
                        @endforeach
                    </div>
                @endif

                @if($question->type === 'paragraph')
                    <textarea class="form-control" rows="3" name="answers[{{ $question->id }}]" {{ $question->is_required ? 'required' : '' }}></textarea>
                @elseif($question->type === 'multiple_choice')
                    @if($question->allow_multiple)
                        <small class="text-muted d-block mb-2">Selecciona hasta {{ $question->max_selections ?? 1 }} opciones.</small>
                    @endif
                    @foreach($question->options ?? [] as $index => $option)
                        <div class="form-check option-choice-row">
                            @if($question->allow_multiple)
                                <input class="form-check-input multiple-choice-input" type="checkbox" name="answers[{{ $question->id }}][]" value="{{ $option }}" id="q{{ $question->id }}o{{ $index }}" data-max-selections="{{ $question->max_selections ?? 1 }}">
                            @else
                                <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" value="{{ $option }}" id="q{{ $question->id }}o{{ $index }}" {{ $question->is_required ? 'required' : '' }}>
                            @endif
                            <label class="form-check-label" for="q{{ $question->id }}o{{ $index }}">{{ $option }}</label>
                            @if(!empty($question->option_images[$index] ?? null))
                                @php
                                    $optionImageSizeClass = match ($question->image_size ?? 'medium') {
                                        'small' => 'option-media-item--small',
                                        'large' => 'option-media-item--large',
                                        default => 'option-media-item--medium',
                                    };
                                @endphp
                                @php($optionImage = $question->option_images[$index])
                                @if(str_starts_with($optionImage, 'data:') || str_starts_with($optionImage, 'http') || str_starts_with($optionImage, '/'))
                                    <img src="{{ $optionImage }}" alt="Imagen de opción" class="option-media-item {{ $optionImageSizeClass }}">
                                @else
                                    <img src="{{ Storage::disk('public')->url($optionImage) }}" alt="Imagen de opción" class="option-media-item {{ $optionImageSizeClass }}">
                                @endif
                            @endif
                        </div>
                    @endforeach
                @elseif($question->type === 'scale')
                    <div>
                        @for($i=1;$i<=5;$i++)
                            <label class="me-3">
                                <input type="radio" name="answers[{{ $question->id }}]" value="{{ $i }}" {{ $question->is_required ? 'required' : '' }}>
                                {{ $i }}
                            </label>
                        @endfor
                    </div>
                    <small>1 = Muy malo, 5 = Excelente</small>
                @else
                    <input class="form-control" name="answers[{{ $question->id }}]" {{ $question->is_required ? 'required' : '' }}>
                @endif

                @error("answers.{$question->id}")
                    <div class="text-danger">{{ $message }}</div>
                @enderror
            </div>
        </section>
    @endforeach

    @if(($survey->show_submit_button ?? true))
        <button class="btn btn-primary btn-lg" style="background:{{ $survey->primary_color ?: '#1e5bb0' }}; border-color:{{ $survey->primary_color ?: '#1e5bb0' }};">{{ $survey->button_text ?: 'Enviar respuestas' }}</button>
    @endif
</form>
@endsection
@push('styles')
<style>
    .question-media-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: .75rem;
        margin: 0 0 1rem;
    }

    .question-media-item {
        width: 100%;
        object-fit: cover;
        border-radius: 14px;
        border: 1px solid #ead8c7;
    }

    .question-media-item--small { height: 110px; }
    .question-media-item--medium { height: 180px; }
    .question-media-item--large { height: 260px; }

    .option-choice-row {
        display: flex;
        align-items: center;
        gap: .75rem;
        margin: .8rem 0;
    }

    .option-media-item {
        object-fit: cover;
        border-radius: 12px;
        border: 1px solid #ead8c7;
    }

    .option-media-item--small { width: 48px; height: 48px; }
    .option-media-item--medium { width: 72px; height: 72px; }
    .option-media-item--large { width: 96px; height: 96px; }
</style>
@endpush
@push('scripts')
<script>
const locationStatusEl = document.getElementById('location-status');
const latitudeField = document.getElementById('latitude');
const longitudeField = document.getElementById('longitude');

const timezoneField = document.getElementById('timezone');
const localeField = document.getElementById('locale');

if (timezoneField) {
    timezoneField.value = Intl.DateTimeFormat().resolvedOptions().timeZone || '';
}

if (localeField) {
    localeField.value = navigator.language || '';
}

document.getElementById('locate')?.addEventListener('click', () => {
    if (!navigator.geolocation) {
        if (locationStatusEl) {
            locationStatusEl.textContent = 'Tu navegador no admite ubicación';
        }
        return;
    }

    navigator.geolocation.getCurrentPosition((position) => {
        if (latitudeField) latitudeField.value = position.coords.latitude;
        if (longitudeField) longitudeField.value = position.coords.longitude;
        if (locationStatusEl) locationStatusEl.textContent = 'Ubicación obtenida';
    }, () => {
        if (locationStatusEl) locationStatusEl.textContent = 'No se pudo obtener la ubicación';
    });
});

const multiChoiceInputs = document.querySelectorAll('.multiple-choice-input');
multiChoiceInputs.forEach((input) => {
    input.addEventListener('change', function () {
        const maxSelections = Number(this.dataset.maxSelections || 1);
        const checkedBoxes = Array.from(document.querySelectorAll(`input[name="${this.name}"]`)).filter((box) => box.checked);
        if (checkedBoxes.length > maxSelections) {
            this.checked = false;
            alert(`Solo puedes seleccionar hasta ${maxSelections} opciones.`);
        }
    });
});

// Barra de progreso: cuenta preguntas respondidas en tiempo real
function updateSurveyProgress() {
    const sections = document.querySelectorAll('section[data-question]');
    const totalQuestions = sections.length;
    if (totalQuestions === 0) return;

    let answered = 0;
    sections.forEach((section) => {
        const isFilled = Array.from(section.querySelectorAll('input, textarea, select')).some((field) => {
            if (field.type === 'radio' || field.type === 'checkbox') return field.checked;
            return String(field.value || '').trim() !== '';
        });
        if (isFilled) answered++;
    });

    const percent = Math.round((answered / totalQuestions) * 100);
    const fillEl = document.getElementById('progress-bar-fill');
    const countEl = document.getElementById('progress-count');
    const completeMsg = document.getElementById('progress-complete-msg');

    if (fillEl) fillEl.style.width = percent + '%';
    if (countEl) countEl.textContent = `${answered} / ${totalQuestions}`;

    if (completeMsg) {
        if (percent === 100) {
            completeMsg.style.display = 'block';
            requestAnimationFrame(() => { completeMsg.style.opacity = '1'; });
            if (fillEl) fillEl.style.boxShadow = '0 0 8px ' + (fillEl.style.background || '#1e5bb0');
        } else {
            completeMsg.style.opacity = '0';
            setTimeout(() => { if (percent < 100) completeMsg.style.display = 'none'; }, 400);
            if (fillEl) fillEl.style.boxShadow = 'none';
        }
    }
}

const surveyFields = document.querySelectorAll('section[data-question] input, section[data-question] textarea, section[data-question] select');
surveyFields.forEach((field) => {
    field.addEventListener('input', updateSurveyProgress);
    field.addEventListener('change', updateSurveyProgress);
});

document.addEventListener('DOMContentLoaded', updateSurveyProgress);
updateSurveyProgress();
</script>
@endpush
