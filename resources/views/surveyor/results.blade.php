@extends('layouts.app')

@section('title', 'Resultados asignados')

@section('content')
    <a href="{{ route('surveyor.dashboard') }}">← Volver a mis resultados</a><div class="results-heading mt-4"><span class="eyebrow">Resultados asignados</span><h1>{{ $survey->title }}</h1><p>{{ $survey->description }}</p></div>
    <div class="summary-grid mb-5"><div class="summary-card"><strong>{{ $survey->submissions->count() }}</strong><span>Respuestas</span></div><div class="summary-card"><strong>{{ $survey->questions->count() }}</strong><span>Preguntas</span></div><div class="summary-card"><strong>{{ $survey->submissions->whereNotNull('latitude')->count() }}</strong><span>Ubicaciones</span></div></div>
    @foreach($survey->questions as $question)<article class="card mb-3"><div class="card-header">{{ $question->text }}</div><ul class="list-group">@forelse($question->answers as $answer)<li class="list-group-item">{{ $answer->value }} <small class="text-muted">{{ $answer->created_at->timezone('America/Lima')->format('d/m/Y H:i') }} (Perú)</small></li>@empty<li class="list-group-item text-muted">Sin respuestas aún</li>@endforelse</ul></article>@endforeach
@endsection
