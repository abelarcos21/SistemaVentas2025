@extends('adminlte::auth.auth-page', ['auth_type' => 'register'])

@section('auth_header', 'Crear una cuenta')

@push('css')
    <link rel="stylesheet" href="{{ asset('css/custom-login.css') }}">
@endpush

@section('auth_body')
    <form action="{{ route('register') }}" method="post">
        @csrf

        {{-- Name --}}
        <div class="input-group mb-3">
            <input type="text" name="name" class="form-control" placeholder="Nombre completo" required autofocus>
            <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-user"></span></div>
            </div>
        </div>

        {{-- Email --}}
        <div class="input-group mb-3">
            <input type="email" name="email" class="form-control" placeholder="Correo electrónico" required>
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

        {{-- Confirm Password --}}
        <div class="input-group mb-3">
            <input type="password" name="password_confirmation" class="form-control" placeholder="Confirmar contraseña" required>
            <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-check"></span></div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Registrarse</button>
    </form>
@endsection

@section('auth_footer')
    <p class="my-0"><a href="{{ route('login') }}" class="text-white">Ya tengo una cuenta</a></p>
@endsection
