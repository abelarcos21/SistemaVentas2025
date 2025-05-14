@extends('adminlte::auth.auth-page', ['auth_type' => 'login'])

@section('auth_header', 'Iniciar sesión')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/custom-login.css') }}">
@endpush

@section('auth_body')
    <form action="{{ route('login') }}" method="post">
        @csrf

        {{-- Email --}}
        <div class="input-group mb-3">
            <input type="email" name="email" class="form-control" placeholder="Correo electrónico" required autofocus>
            <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-envelope"></span></div>
            </div>
        </div>

        {{-- Password --}}
        <div class="input-group mb-3">
            <input type="password" name="password" class="form-control" placeholder="Contraseña" required>
            <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-lock"></span></div>
            </div>
        </div>

        {{-- Remember Me --}}
        <div class="row">
            <div class="col-8">
                <div class="icheck-primary">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Recordarme</label>
                </div>
            </div>
            <div class="col-4">
                <x-adminlte-button class="btn-sm" type="submit" label="Acceder" icon="fas fa-sign-in-alt"/>
            </div>
        </div>
    </form>
@endsection

@section('auth_footer')
    <p class="my-0 text-blank"><a href="{{ route('password.request') }}" class="text-white">¿Olvidaste tu contraseña?</a></p>
    <p class="my-0"><a href="{{ route('register') }}" class="text-white">Registrarse</a></p>
@endsection



