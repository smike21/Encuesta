@extends('layouts.app')

@section('title', 'Resultados')

@section('content')
    <div class="results-actions">
        <a href="{{ route('admin.dashboard') }}">← Volver al panel</a>
        <div class="d-flex align-items-center gap-2">
            @php $locationCount = $survey->submissions->whereNotNull('latitude')->whereNotNull('longitude')->count(); @endphp
            @if($locationCount > 0)
                <button type="button" class="btn btn-outline-primary" id="show-map-btn">Mostrar mapa de ubicaciones</button>
            @endif
            <a class="btn btn-primary" href="{{ route('admin.export', $survey) }}">Descargar resultados en Excel</a>
        </div>
    </div>

    <div id="results-map-wrapper" class="mb-4" style="display:none;">
        <div class="section-title"><span class="eyebrow">Mapa de ubicaciones</span><h2>Ubicaciones de respuestas</h2></div>
        <div id="results-map" style="width:100%; min-height:420px; border:1px solid #ddd; border-radius:12px;"></div>
    </div>

    <div class="results-heading">
        <span class="eyebrow">Resultados de la encuesta</span>
        <h1>{{ $survey->title }}</h1>
        <p>{{ $survey->description }}</p>
    </div>

    <div class="summary-grid mb-5">
        <div class="summary-card"><strong>{{ $survey->submissions->count() }}</strong><span>Respuestas recibidas</span></div>
        <div class="summary-card"><strong>{{ $survey->questions->count() }}</strong><span>Preguntas</span></div>
        <div class="summary-card"><strong>{{ $survey->submissions->whereNotNull('latitude')->count() }}</strong><span>Ubicaciones registradas</span></div>
    </div>

    <section class="statistics-section">
        <div class="section-title"><span class="eyebrow">Análisis visual</span><h2>Estadísticas</h2><p>Los gráficos resumen las respuestas de opción múltiple y escala.</p></div>
        <div class="statistics-grid">
            @foreach($survey->questions as $question)
                @php
                    $counts = $question->answers->countBy(fn($answer) => $answer->value);
                    $choices = $question->type === 'multiple_choice' ? collect($question->options ?? []) : ($question->type === 'scale' ? collect(range(1, 5))->map(fn($value) => (string) $value) : $counts->keys());
                    $maximum = max(1, $counts->max() ?? 0);
                @endphp
                <article class="card statistic-card">
                    <div class="card-body">
                        <span class="question-kind">{{ $question->type === 'scale' ? 'Escala 1–5' : ($question->type === 'multiple_choice' ? 'Opción múltiple' : 'Respuesta abierta') }}</span>
                        <h3>{{ $question->text }}</h3>
                        @if(in_array($question->type, ['multiple_choice', 'scale']))
                            <div class="bar-chart">
                                @foreach($choices as $choice)
                                    @php $total = $counts->get((string) $choice, 0); $percent = round(($total / $maximum) * 100); @endphp
                                    <div class="bar-row"><div class="bar-label">{{ $choice }}</div><div class="bar-track"><span class="bar-fill" style="width: {{ $percent }}%"></span></div><strong>{{ $total }}</strong></div>
                                @endforeach
                            </div>
                        @else
                            <div class="open-answer-stat"><strong>{{ $question->answers->count() }}</strong><span>respuestas abiertas</span></div>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    </section>

    <section class="mt-5"><div class="section-title"><span class="eyebrow">Detalle</span><h2>Respuestas por pregunta</h2></div>
        @foreach($survey->questions as $question)
            <article class="card mb-3"><div class="card-header">{{ $question->text }}</div><ul class="list-group">
                @forelse($question->answers as $answer)<li class="list-group-item">{{ $answer->value }} <small class="text-muted">{{ $answer->created_at->timezone('America/Lima')->format('d/m/Y H:i') }} (Perú)</small></li>
                @empty<li class="list-group-item text-muted">Sin respuestas aún</li>@endforelse
            </ul></article>
        @endforeach
    </section>

    <section class="mt-5"><div class="section-title"><span class="eyebrow">Registro de horario</span><h2>Horarios y ubicaciones</h2></div>
        <ul class="list-group card">
            @forelse($survey->submissions as $submission)
                <li class="list-group-item time-row">
                    <div><strong>Horario peruano:</strong> {{ $submission->created_at->timezone('America/Lima')->format('d/m/Y H:i') }}</div>
                    <div><strong>Horario de {{ $submission->countryLabel() }}:</strong> {{ $submission->timezone ? $submission->created_at->timezone($submission->timezone)->format('d/m/Y H:i') : 'No registrado' }}</div>
                    <div class="text-muted small">{{ $submission->latitude !== null ? $submission->latitude.', '.$submission->longitude : 'Ubicación no disponible' }} @if($submission->latitude !== null)<a target="_blank" href="https://www.google.com/maps/search/?api=1&query={{ $submission->latitude }},{{ $submission->longitude }}">Ver mapa</a>@endif</div>
                </li>
            @empty<li class="list-group-item text-muted">Sin respuestas todavía.</li>@endforelse
        </ul>
    </section>
@endsection

@push('styles')
<style>
    #results-map { border-radius: 16px; }
</style>
@endpush

@push('scripts')
@php
    $locations = $survey->submissions
        ->filter(fn($submission) => $submission->latitude !== null && $submission->longitude !== null)
        ->map(fn($submission) => [
            'lat' => $submission->latitude,
            'lng' => $submission->longitude,
            'label' => $submission->created_at->timezone('America/Lima')->format('d/m/Y H:i').' · '.$submission->countryLabel(),
            'link' => 'https://www.google.com/maps/search/?api=1&query=' . $submission->latitude . ',' . $submission->longitude,
        ])
        ->values()
        ->all();
@endphp
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const showMapBtn = document.getElementById('show-map-btn');
        const mapWrapper = document.getElementById('results-map-wrapper');
        const locations = @json($locations);
        let mapInitialized = false;
        let map;

        if (!showMapBtn || !mapWrapper) return;

        function loadLeafletAssets(callback) {
            if (window.L) {
                callback();
                return;
            }

            const css = document.createElement('link');
            css.rel = 'stylesheet';
            css.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
            css.integrity = 'sha256-oQmHd6PneB9g1rE0IJt0V24OWw4QqipEvkjsuBB0z2M=';
            css.crossOrigin = '';
            document.head.appendChild(css);

            const script = document.createElement('script');
            script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
            script.integrity = 'sha256-o9N1jV+8BjwE2hPSzP3ozX8mQO8+4atz2BacXSf8xM0=';
            script.crossOrigin = '';
            script.onload = callback;
            script.onerror = function () {
                alert('No se pudo cargar el mapa. Revisa tu conexión o intenta de nuevo.');
            };
            document.body.appendChild(script);
        }

        function initMap() {
            if (mapInitialized) {
                map.invalidateSize();
                return;
            }
            map = L.map('results-map', { scrollWheelZoom: false });
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/">OpenStreetMap</a> contributors'
            }).addTo(map);

            const markers = locations.map(loc => {
                const marker = L.marker([loc.lat, loc.lng]).addTo(map);
                marker.bindPopup(`<strong>${loc.label}</strong><br><a href="${loc.link}" target="_blank">Abrir en Google Maps</a>`);
                return marker;
            });
            const group = L.featureGroup(markers);
            map.fitBounds(group.getBounds().pad(0.15));
            mapInitialized = true;
        }

        showMapBtn.addEventListener('click', function () {
            const isHidden = mapWrapper.style.display === 'none' || mapWrapper.style.display === '';
            mapWrapper.style.display = isHidden ? 'block' : 'none';
            showMapBtn.textContent = isHidden ? 'Ocultar mapa' : 'Mostrar mapa de ubicaciones';
            if (!isHidden) {
                if (window.L) {
                    initMap();
                } else {
                    loadLeafletAssets(initMap);
                }
            }
        });
    });
</script>
@endpush
