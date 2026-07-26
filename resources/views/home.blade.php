@extends('layouts.app')

@section('title', 'Encuestas')

@section('content')
<div class="mb-5">
    <h1>Encuestas</h1>
    <p>Accede a las encuestas disponibles y participa desde aquí.</p>
</div>

<div class="row">
    @forelse($surveys ?? [] as $survey)
        <div class="col-md-6 col-lg-4">
            <article class="card h-100">
                <div class="card-body">
                    <h2 class="h5">{{ $survey->title }}</h2>
                    <p>{{ $survey->description }}</p>
                    <p class="small mt-3 mb-0">{{ $survey->questions_count }} preguntas</p>
                </div>
                <div class="card-footer">
                    <a class="btn w-100" href="{{ route('surveys.show', $survey) }}">Responder</a>
                </div>
            </article>
        </div>
    @empty
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <h2 class="h5">No hay encuestas disponibles</h2>
                    <p>Pronto habrá nuevas encuestas para responder.</p>
                </div>
            </div>
        </div>
    @endforelse
</div>
@endsection
