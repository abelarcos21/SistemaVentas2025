@extends('adminlte::auth.auth-page', ['auth_type' => 'confirm_password'])

@section('auth_header', 'Confirmar contraseña')

@section('auth_body')
    <p class="mb-3">Por favor, confirma tu contraseña antes de continuar.</p>

    <form action="{{ route('password.confirm') }}" method="POST">
        @csrf

        {{-- Password --}}
        <div class="input-group mb-3">
            <input type="password" name="password" class="form-control" placeholder="Contraseña" required autofocus>
            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock"></span>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Confirmar</button>
    </form>
@endsection

@section('auth_footer')
    <p class="my-0">
        <a href="{{ route('password.request') }}">¿Olvidaste tu contraseña?</a>
    </p>
@endsection
