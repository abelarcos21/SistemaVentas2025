@extends('adminlte::auth.auth-page', ['auth_type' => 'password_reset'])

@section('auth_header', 'Restablecer contraseña')

@section('auth_body')
    <form action="{{ route('password.update') }}" method="post">
        @csrf

        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="email" value="{{ $email ?? old('email') }}">

        {{-- Password --}}
        <div class="input-group mb-3">
            <input type="password" name="password" class="form-control" placeholder="Nueva contraseña" required autofocus>
            <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-lock"></span></div>
            </div>
        </div>

        {{-- Confirm Password --}}
        <div class="input-group mb-3">
            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirmar nueva contraseña" required>
            <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-check"></span></div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Restablecer</button>
    </form>
@endsection

@section('auth_footer')
    <p class="my-0"><a href="{{ route('login') }}">Volver al inicio de sesión</a></p>
@endsection
