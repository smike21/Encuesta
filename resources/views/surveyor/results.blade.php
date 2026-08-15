@extends('layouts.app')

@section('title', 'Resultados asignados')

@section('content')
    <a href="{{ route('surveyor.dashboard') }}">← Volver a mis resultados</a>
    <div class="results-heading mt-4"><span class="eyebrow">Resultados asignados</span><h1>{{ $survey->title }}</h1><p>{{ $survey->description }}</p></div>

    <div class="summary-grid mb-5">
        <div class="summary-card"><strong>{{ $survey->submissions->count() }}</strong><span>Encuestas llenadas</span></div>
        <div class="summary-card"><strong>{{ $survey->questions->count() }}</strong><span>Preguntas</span></div>
        <div class="summary-card"><strong>{{ $survey->submissions->whereNotNull('latitude')->count() }}</strong><span>Ubicaciones registradas</span></div>
    </div>

    <section class="statistics-section">
        <div class="section-title"><span class="eyebrow">Análisis visual</span><h2>Estadísticas</h2><p>Resumen de las respuestas por cada pregunta asignada.</p></div>
        <div class="statistics-grid">
            @foreach($survey->questions as $question)
                @php
                    $counts = $question->answers->countBy(fn($answer) => $answer->value);
                    $choices = $question->type === 'multiple_choice' ? collect($question->options ?? []) : ($question->type === 'scale' ? collect(range(1, 5))->map(fn($value) => (string) $value) : $counts->keys());
                    $maximum = max(1, $counts->max() ?? 0);
                @endphp
                <article class="card statistic-card"><div class="card-body">
                    <span class="question-kind">{{ $question->type === 'scale' ? 'Escala 1–5' : ($question->type === 'multiple_choice' ? 'Opción múltiple' : 'Respuesta abierta') }}</span>
                    <h3>{{ $question->text }}</h3>
                    @if(in_array($question->type, ['multiple_choice', 'scale']))
                        <div class="bar-chart">@foreach($choices as $choice)@php $total = $counts->get((string) $choice, 0); $percent = round(($total / $maximum) * 100); @endphp<div class="bar-row"><div class="bar-label">{{ $choice }}</div><div class="bar-track"><span class="bar-fill" style="width: {{ $percent }}%"></span></div><strong>{{ $total }}</strong></div>@endforeach</div>
                    @else
                        <div class="open-answer-stat"><strong>{{ $question->answers->count() }}</strong><span>respuestas abiertas</span></div>
                    @endif
                </div></article>
            @endforeach
        </div>
    </section>

    <section class="mt-5"><div class="section-title"><span class="eyebrow">Detalle específico</span><h2>Respuestas por pregunta</h2></div>
        @foreach($survey->questions as $question)<article class="card mb-3"><div class="card-header">{{ $question->text }}</div><ul class="list-group">@forelse($question->answers as $answer)<li class="list-group-item">{{ $answer->value }} <small class="text-muted">{{ $answer->created_at->timezone('America/Lima')->format('d/m/Y H:i') }} (Perú)</small></li>@empty<li class="list-group-item text-muted">Sin respuestas aún</li>@endforelse</ul></article>@endforeach
    </section>
@endsection
