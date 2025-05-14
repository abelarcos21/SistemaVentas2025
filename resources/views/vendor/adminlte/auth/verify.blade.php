@extends('adminlte::auth.verify')

@section('auth_header', 'Verifica tu correo electrónico')

@section('auth_body')
    <p class="mb-3">
        Antes de continuar, revisa tu correo electrónico para encontrar el enlace de verificación.
        Si no lo recibiste, puedes solicitar uno nuevo.
    </p>

    @if (session('resent'))
        <div class="alert alert-success" role="alert">
            Se ha enviado un nuevo enlace de verificación a tu correo electrónico.
        </div>
    @endif

    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
        @csrf
        <button type="submit" class="btn btn-primary btn-block">Solicitar nuevo enlace</button>
    </form>
@endsection

@section('auth_footer')
    <p class="my-0">
        <a href="{{ route('logout') }}"
           onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            Cerrar sesión
        </a>
    </p>

    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
        @csrf
    </form>
@endsection
