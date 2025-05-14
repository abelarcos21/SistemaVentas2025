@extends('adminlte::auth.auth-page', ['auth_type' => 'password_reset'])

@section('auth_header', 'Recuperar contraseña')

@section('auth_body')
    <form action="{{ route('password.email') }}" method="post">
        @csrf

        <div class="input-group mb-3">
            <input type="email" name="email" class="form-control" placeholder="Correo electrónico" required autofocus>
            <div class="input-group-append">
                <div class="input-group-text"><span class="fas fa-envelope"></span></div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary btn-block">Enviar enlace</button>
    </form>
@endsection

@section('auth_footer')
    <p class="my-0"><a href="{{ route('login') }}">Volver al inicio de sesión</a></p>
@endsection
