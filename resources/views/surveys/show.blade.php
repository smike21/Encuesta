@extends('layouts.app')

@section('title', $survey->title ?? 'Encuesta')

@section('content')
<div class="mb-4">
    <a href="{{ route('surveys.index') }}">← Volver a encuestas</a>
</div>

<div class="card">
    <div class="card-body">
        <h1>{{ $survey->title }}</h1>
        @if($survey->description)
            <p class="text-muted">{{ $survey->description }}</p>
        @endif

        <form method="POST" action="{{ route('surveys.submit', $survey) }}" id="survey-form">
            @csrf

            @foreach($survey->questions as $question)
                <div class="mb-3">
                    <label class="form-label"><strong>{{ $question->text }}</strong> @if($question->is_required)<span class="text-danger">*</span>@endif</label>

                    @if($question->type === 'text')
                        <input type="text" name="answers[{{ $question->id }}]" class="form-control" value="" {{ $question->is_required ? 'required' : '' }}>

                    @elseif($question->type === 'paragraph')
                        <textarea name="answers[{{ $question->id }}]" class="form-control" rows="3" {{ $question->is_required ? 'required' : '' }}></textarea>

                    @elseif($question->type === 'multiple_choice')
                        @if($question->allow_multiple)
                            @foreach($question->options ?? [] as $option)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="answers[{{ $question->id }}][]" value="{{ $option }}" id="q{{ $question->id }}_{{ \Illuminate\Support\Str::slug($option) }}">
                                    <label class="form-check-label" for="q{{ $question->id }}_{{ \Illuminate\Support\Str::slug($option) }}">{{ $option }}</label>
                                </div>
                            @endforeach
                        @else
                            @foreach($question->options ?? [] as $option)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" value="{{ $option }}" id="q{{ $question->id }}_{{ \Illuminate\Support\Str::slug($option) }}">
                                    <label class="form-check-label" for="q{{ $question->id }}_{{ \Illuminate\Support\Str::slug($option) }}">{{ $option }}</label>
                                </div>
                            @endforeach
                        @endif

                    @elseif($question->type === 'scale')
                        <div class="d-flex gap-2">
                            @foreach(range(1,5) as $n)
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="answers[{{ $question->id }}]" value="{{ $n }}" id="q{{ $question->id }}_{{ $n }}">
                                    <label class="form-check-label" for="q{{ $question->id }}_{{ $n }}">{{ $n }}</label>
                                </div>
                            @endforeach
                        </div>

                    @else
                        <input type="text" name="answers[{{ $question->id }}]" class="form-control" value="" {{ $question->is_required ? 'required' : '' }}>
                    @endif

                </div>
            @endforeach

            @if($survey->collect_location)
                <div class="mb-3">
                    <p class="small text-muted">Esta encuesta solicita tu ubicación. Presiona el botón para compartir ubicación o envíala sin ubicación.</p>
                    <button type="button" id="get-location" class="btn btn-outline-secondary btn-sm">Obtener mi ubicación</button>
                    <div id="location-status" class="small text-muted mt-2">Ubicación: no disponible</div>
                    <input type="hidden" name="latitude" id="latitude">
                    <input type="hidden" name="longitude" id="longitude">
                </div>
            @endif

            <div class="d-flex align-items-center gap-3">
                <button type="submit" class="btn btn-primary">Enviar respuesta</button>
                <a href="{{ route('surveys.index') }}" class="btn btn-link">Cancelar</a>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
@if($survey->collect_location)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('get-location');
    const status = document.getElementById('location-status');
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');

    btn.addEventListener('click', function () {
        if (!navigator.geolocation) {
            status.textContent = 'Geolocalización no soportada por este navegador.';
            return;
        }
        status.textContent = 'Solicitando ubicación...';
        navigator.geolocation.getCurrentPosition(function (pos) {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            latInput.value = lat;
            lngInput.value = lng;
            status.textContent = 'Ubicación capturada: ' + lat.toFixed(5) + ', ' + lng.toFixed(5);
        }, function (err) {
            status.textContent = 'No se pudo obtener la ubicación: ' + (err.message || 'denegada');
        }, { enableHighAccuracy: true, timeout: 10000 });
    });
});
</script>
@endif
@endpush
