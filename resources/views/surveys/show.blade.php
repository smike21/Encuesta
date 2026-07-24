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

<form method="post" action="{{ route('surveys.submit',$survey) }}">
    @csrf
    <input type="hidden" name="timezone" id="timezone">
    <input type="hidden" name="locale" id="locale">

    @if($survey->collect_location)
        <section class="card mb-3">
            <div class="card-body">
                <h2 class="h5">Compartir ubicación (opcional)</h2>
                <button type="button" class="btn btn-outline-primary" id="locate">Obtener mi ubicación</button>
                <span id="location-status" class="ms-2"></span>
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">
            </div>
        </section>
    @endif

    @foreach($survey->questions as $question)
        <section class="card mb-3">
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

    <button class="btn btn-primary btn-lg">Enviar respuestas</button>
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
document.getElementById('timezone').value = Intl.DateTimeFormat().resolvedOptions().timeZone || '';
document.getElementById('locale').value = navigator.language || '';
document.getElementById('locate')?.addEventListener('click', () => navigator.geolocation ? navigator.geolocation.getCurrentPosition(p => {
    latitude.value = p.coords.latitude;
    longitude.value = p.coords.longitude;
    document.getElementById('location-status').textContent = 'Ubicación obtenida';
}, () => document.getElementById('location-status').textContent = 'No se pudo obtener la ubicación') : document.getElementById('location-status').textContent = 'Tu navegador no admite ubicación');

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
</script>
@endpush
