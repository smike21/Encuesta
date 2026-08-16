@extends('layouts.app')

@section('title', 'Encuestadores')

@section('content')
    <div class="results-actions">
        <div><span class="eyebrow">Administración</span><h1>Encuestadores</h1><p>Crea cuentas y decide qué resultados puede revisar cada encuestador.</p></div>
        <a class="btn btn-primary" href="{{ route('admin.surveyors.create') }}">Crear encuestador</a>
    </div>
    <a href="{{ route('admin.dashboard') }}">← Volver al panel</a>

    <div class="account-list mt-4">
        @forelse($surveyors as $surveyor)
            <article class="card account-card">
                <div class="card-body"><div class="account-title"><div><h2 class="h5">{{ $surveyor->name }}</h2><p>{{ $surveyor->email }}</p></div><span class="badge text-bg-{{ $surveyor->is_active ? 'success' : 'secondary' }}">{{ $surveyor->is_active ? 'Habilitado' : 'Inhabilitado' }}</span></div>
                    <p class="small"><strong>{{ $surveyor->assigned_surveys_count }}</strong> encuestas asignadas · <strong>{{ $surveyor->assignedSurveys->sum('submissions_count') }}</strong> respuestas visibles</p>
                    @if($surveyor->assignedSurveys->isNotEmpty())<p class="small text-muted">{{ $surveyor->assignedSurveys->pluck('title')->join(', ') }}</p>@endif
                </div>
                <div class="card-footer d-flex flex-wrap gap-2"><a class="btn btn-sm btn-outline-primary" href="{{ route('admin.surveyors.access', $surveyor) }}">Gestionar resultados</a><form method="post" action="{{ route('admin.surveyors.toggle', $surveyor) }}">@csrf @method('PATCH')<button class="btn btn-sm btn-outline-warning">{{ $surveyor->is_active ? 'Inhabilitar' : 'Habilitar' }}</button></form></div>
            </article>
        @empty
            <div class="card"><div class="card-body"><h2 class="h4">Aún no hay encuestadores</h2><p>Crea una cuenta para asignarle acceso a resultados específicos.</p></div></div>
        @endforelse
    </div>
@endsection
