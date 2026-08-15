@extends('layouts.app')

@section('title', 'Permisos de resultados')

@section('content')
    <div class="form-shell"><a href="{{ route('admin.surveyors') }}">← Volver a encuestadores</a><div class="card mt-4"><div class="card-body"><span class="eyebrow">Permisos de resultados</span><h1>{{ $surveyor->name }}</h1><p>Selecciona las encuestas cuyos resultados podrá revisar esta cuenta.</p>
        <form method="post" action="{{ route('admin.surveyors.access.update', $surveyor) }}">@csrf @method('PUT')
            <div class="permission-list">@forelse($surveys as $survey)<label class="permission-item"><input type="checkbox" name="surveys[]" value="{{ $survey->id }}" @checked(in_array($survey->id, $assignedIds))><span><strong>{{ $survey->title }}</strong><small>{{ $survey->submissions_count }} respuestas · {{ $survey->is_active ? 'Activa' : 'Inactiva' }}</small></span></label>@empty<p>No hay encuestas creadas.</p>@endforelse</div>
            <button class="btn btn-primary mt-4">Guardar permisos</button>
        </form></div></div></div>
@endsection
