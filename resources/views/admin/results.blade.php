@extends('layouts.app')

@section('title', 'Resultados')

@section('content')
    <div class="results-actions">
        <a href="{{ route('admin.dashboard') }}">← Volver al panel</a>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            @php $locationCount = $survey->submissions->whereNotNull('latitude')->whereNotNull('longitude')->count(); @endphp
            @if($locationCount > 0)
                <button type="button" class="btn btn-outline-primary" id="show-map-btn">Mostrar mapa de ubicaciones</button>
                <a class="btn btn-outline-success" href="{{ route('admin.export_kml', $survey) }}" download>Descargar mapa KML</a>
            @endif
            <button type="button" class="btn btn-outline-warning" id="show-ip-summary-btn">Ver IPs repetidas</button>
            <button type="button" class="btn btn-outline-secondary" id="show-surveyor-counts-btn">Ver conteo por encuestador de hoy</button>
            <a class="btn btn-primary" href="{{ route('admin.export', $survey) }}">Descargar resultados en Excel</a>
        </div>
    </div>

    <div id="results-map-wrapper" class="mb-4" style="display:none;">
        <div class="section-title"><span class="eyebrow">Mapa de ubicaciones</span><h2>Ubicaciones marcadas por los participantes</h2></div>
        <div class="map-status text-muted small mb-2">Haz clic en el botón para cargar el mapa.</div>
        <div id="results-map" style="width:100%; min-height:420px; border:1px solid #ddd; border-radius:12px;"></div>
    </div>

    <div id="ip-summary-wrapper" class="mb-4" style="display:none;">
        <div class="section-title"><span class="eyebrow">IPs</span><h2>Direcciones IP registradas por respuesta</h2></div>
        <div id="ipSummaryContainer"></div>
    </div>

    <div id="surveyor-counts-wrapper" class="mb-4" style="display:none;">
        <div class="section-title"><span class="eyebrow">Encuestadores</span><h2>Conteo de respuestas por encuestador (hoy)</h2></div>
        <div id="surveyorCountsContainer"></div>
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

@php
    $submissionLocations = $survey->submissions
        ->whereNotNull('latitude')
        ->whereNotNull('longitude')
        ->map(function ($submission) {
            return [
                'lat' => $submission->latitude,
                'lng' => $submission->longitude,
                'label' => $submission->created_at->timezone('America/Lima')->format('d/m/Y H:i'),
                'ip_address' => $submission->ip_address ?? 'No disponible',
                'url' => 'https://www.google.com/maps/search/?api=1&query=' . $submission->latitude . ',' . $submission->longitude,
            ];
        })
        ->values();
@endphp

@push('scripts')
<script>
    window.resultsMapLocations = @json($submissionLocations);
</script>
<script src="{{ asset('js/results-map.js') }}"></script>
<script>
    (function () {
        function escapeHtml(value) {
            value = String(value ?? '');
            return value
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#039;');
        }

        var ipButton = document.getElementById('show-ip-summary-btn');
        var ipWrapper = document.getElementById('ip-summary-wrapper');
        var ipContainer = document.getElementById('ipSummaryContainer');
        if (ipButton && ipWrapper && ipContainer) {
            var ipUrl = @json(route('admin.ip_summary', $survey));
            var ipLoaded = false;

            function renderIpSpinner() {
                ipContainer.innerHTML = '<div class="text-muted small py-3">Cargando direcciones IP…</div>';
            }

            function renderIpError(message) {
                ipContainer.innerHTML = '<div class="alert alert-danger">' + (message || 'No se pudieron cargar los IPs.') + '</div>';
            }

            function renderIpTable(rows) {
                if (!Array.isArray(rows) || rows.length === 0) {
                    ipContainer.innerHTML = '<div class="text-muted small py-3">No hay IPs registradas para esta encuesta.</div>';
                    return;
                }

                var html = '<table class="table table-bordered table-striped">';
                html += '<thead><tr><th>IP</th><th>Veces repetida</th></tr></thead><tbody>';
                rows.forEach(function (row) {
                    var ip = row.ip_address != null ? String(row.ip_address) : 'No disponible';
                    var count = row.count != null ? row.count : 0;
                    html += '<tr>'
                        + '<td>' + escapeHtml(ip) + '</td>'
                        + '<td>' + escapeHtml(String(count)) + '</td>'
                        + '</tr>';
                });
                html += '</tbody></table>';
                ipContainer.innerHTML = html;
            }

            function loadIpSummary() {
                renderIpSpinner();
                fetch(ipUrl, {
                    headers: { 'Accept': 'application/json' },
                    credentials: 'same-origin',
                })
                    .then(function (response) {
                        if (!response.ok) throw new Error('HTTP ' + response.status);
                        return response.json();
                    })
                    .then(function (data) {
                        renderIpTable(data);
                    })
                    .catch(function () {
                        renderIpError();
                    });
            }

            ipButton.addEventListener('click', function () {
                var isHidden = ipWrapper.style.display === 'none' || !ipWrapper.style.display;
                ipWrapper.style.display = isHidden ? 'block' : 'none';
                if (isHidden && !ipLoaded) {
                    ipLoaded = true;
                    loadIpSummary();
                }
            });
        }

        var button = document.getElementById('show-surveyor-counts-btn');
        var wrapper = document.getElementById('surveyor-counts-wrapper');
        var container = document.getElementById('surveyorCountsContainer');
        if (!button || !wrapper || !container) return;

        var countsUrl = @json(route('admin.surveyor_counts', $survey));
        var loaded = false;

        function renderSpinner() {
            container.innerHTML = '<div class="text-muted small py-3">Cargando conteo de encuestadores…</div>';
        }

        function renderError(message) {
            container.innerHTML = '<div class="alert alert-danger">' + (message || 'Ocurrió un error al cargar el conteo por encuestador. Intenta nuevamente.') + '</div>';
        }

        function renderTable(rows) {
            if (!Array.isArray(rows) || rows.length === 0) {
                container.innerHTML = '<div class="text-muted small py-3">No hay respuestas registradas hoy para esta encuesta.</div>';
                return;
            }

            var html = '<table class="table table-bordered table-striped">';
            html += '<thead><tr><th>Encuestador</th><th>Email</th><th>Respuestas</th></tr></thead><tbody>';
            rows.forEach(function (row) {
                var name = row.surveyor_name != null ? String(row.surveyor_name) : 'Anónimo';
                var email = row.surveyor_email != null ? String(row.surveyor_email) : '';
                var count = row.count != null ? row.count : 0;
                html += '<tr>'
                    + '<td>' + escapeHtml(name) + '</td>'
                    + '<td>' + escapeHtml(email) + '</td>'
                    + '<td>' + escapeHtml(String(count)) + '</td>'
                    + '</tr>';
            });
            html += '</tbody></table>';
            container.innerHTML = html;
        }

        function loadCounts() {
            renderSpinner();
            fetch(countsUrl, {
                headers: { 'Accept': 'application/json' },
                credentials: 'same-origin',
            })
                .then(function (response) {
                    if (!response.ok) throw new Error('HTTP ' + response.status);
                    return response.json();
                })
                .then(function (data) {
                    renderTable(data);
                })
                .catch(function () {
                    renderError();
                });
        }

        button.addEventListener('click', function () {
            var isHidden = wrapper.style.display === 'none' || !wrapper.style.display;
            wrapper.style.display = isHidden ? 'block' : 'none';
            if (isHidden && !loaded) {
                loaded = true;
                loadCounts();
            }
        });
    })();
</script>
@endpush
