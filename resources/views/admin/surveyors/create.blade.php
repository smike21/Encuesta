@extends('layouts.app')

@section('title', 'Crear encuestador')

@section('content')
    <div class="form-shell"><a href="{{ route('admin.surveyors') }}">← Volver a encuestadores</a><div class="card mt-4"><div class="card-body"><span class="eyebrow">Nueva cuenta</span><h1>Crear encuestador</h1><p>Esta cuenta solo podrá ver los resultados que le asignes.</p>
        <form method="post" action="{{ route('admin.surveyors.store') }}">@csrf
            <label class="form-label">Nombre completo</label><input class="form-control mb-3" name="name" value="{{ old('name') }}" required>
            <label class="form-label">Correo electrónico</label><input class="form-control mb-3" type="email" name="email" value="{{ old('email') }}" required>@error('email')<p class="text-danger small">{{ $message }}</p>@enderror
            <label class="form-label">Contraseña temporal</label><input class="form-control mb-3" type="password" name="password" minlength="8" required>
            <label class="form-label">Confirmar contraseña</label><input class="form-control mb-4" type="password" name="password_confirmation" minlength="8" required>
            <button class="btn btn-primary">Crear cuenta</button>
        </form></div></div></div>
@endsection
