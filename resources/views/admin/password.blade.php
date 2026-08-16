@extends('layouts.app')

@section('title', 'Cambiar contraseña')

@section('content')
    <div class="card mx-auto" style="max-width:540px;">
        <div class="card-header">Cambiar contraseña de administrador</div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.password.update') }}">
                @csrf

                <div class="mb-4">
                    <label for="current_password" class="form-label">Contraseña actual</label>
                    <input id="current_password" type="password" name="current_password" class="form-control" required autocomplete="current-password">
                    @error('current_password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Nueva contraseña</label>
                    <input id="password" type="password" name="password" class="form-control" required autocomplete="new-password">
                    @error('password')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label for="password_confirmation" class="form-label">Confirmar nueva contraseña</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" class="form-control" required autocomplete="new-password">
                </div>

                <button type="submit" class="btn w-100">Actualizar contraseña</button>
            </form>
        </div>
    </div>
@endsection
