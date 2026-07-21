@extends('layouts.app')

@section('title', 'Panel de encuestador')

@section('content')
    <span class="eyebrow">Panel de encuestador</span><h1>Resultados asignados</h1><p>Solo puedes acceder a las encuestas autorizadas por el administrador.</p>
    <div class="row mt-4">@forelse($surveys as $survey)<div class="col-md-6 col-lg-4"><article class="card h-100"><div class="card-body"><h2 class="h5">{{ $survey->title }}</h2><p>{{ $survey->description }}</p><p class="small">{{ $survey->questions_count }} preguntas · {{ $survey->submissions_count }} respuestas</p></div><div class="card-footer"><a class="btn btn-primary" href="{{ route('surveyor.results', $survey) }}">Ver resultados</a></div></article></div>@empty<p class="text-muted">El administrador aún no te ha asignado resultados de encuestas.</p>@endforelse</div>
@endsection
