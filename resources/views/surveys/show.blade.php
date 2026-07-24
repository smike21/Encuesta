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

                @if(!empty($question->question_images))
                    <div class="question-media-grid">
                        @foreach($question->question_images as $imagePath)
                            <img src="{{ asset('storage/' . ltrim($imagePath, '/')) }}" alt="Imagen de pregunta" class="question-media-item">
                        @endforeach
                    </div>
                @endif

                @if($question->type === 'paragraph')
                    <textarea class="form-control" rows="3" name="answers[{{ $question->id }}]" required></textarea>
                @elseif($question->type === 'multiple_choice')
                    @foreach($question->options ?? [] as $index => $option)
                        <div class="form-check option-choice-row">
                            <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" value="{{ $option }}" id="q{{ $question->id }}o{{ $index }}" required>
                            <label class="form-check-label" for="q{{ $question->id }}o{{ $index }}">{{ $option }}</label>
                            @if(!empty($question->option_images[$index] ?? null))
                                <img src="{{ asset('storage/' . ltrim($question->option_images[$index], '/')) }}" alt="Imagen de opción" class="option-media-item">
                            @endif
                        </div>
                    @endforeach
                @elseif($question->type === 'scale')
                    <div>
                        @for($i=1;$i<=5;$i++)
                            <label class="me-3">
                                <input type="radio" name="answers[{{ $question->id }}]" value="{{ $i }}" required>
                                {{ $i }}
                            </label>
                        @endfor
                    </div>
                    <small>1 = Muy malo, 5 = Excelente</small>
                @else
                    <input class="form-control" name="answers[{{ $question->id }}]" required>
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
        height: 180px;
        object-fit: cover;
        border-radius: 14px;
        border: 1px solid #ead8c7;
    }

    .option-choice-row {
        display: flex;
        align-items: center;
        gap: .75rem;
        margin: .8rem 0;
    }

    .option-media-item {
        width: 72px;
        height: 72px;
        object-fit: cover;
        border-radius: 12px;
        border: 1px solid #ead8c7;
    }
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
</script>
@endpush
