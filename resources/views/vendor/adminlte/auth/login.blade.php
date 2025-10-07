@extends('adminlte::auth.auth-page', ['authType' => 'login'])


@section('adminlte_css')
    <link rel="stylesheet" href="{{ asset('vendor/icheck-bootstrap/icheck-bootstrap.min.css') }}">

    <style>
        /* custom-login.css */

        /* Para ambas páginas: login Y register */
        .login-page, .register-page {
            background-color: #fff !important;

        }

        /*aclarar el card/formulario */
        .login-box, .register-box {
            background-color: #ffffff !important;
            border: 1px solid #e0e0e0 !important;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            color: #333;
        }

        .login-logo a, .register-logo a {
            color: #333 !important;
        }

        /* Estilos adicionales para que se vea bien */
        .card {
            background-color: #2a2a40 !important;
            border: 1px solid #1e1e2f !important;
            color: white !important;
        }

        .card-header {
            background-color: transparent !important;
            border-bottom: 1px solid #444 !important;
            color: white !important;
        }

       /*  .form-control {
            background-color: #1e1e2f !important;
            border: 1px solid #444 !important;
            color: white !important;
        }

        .form-control:focus {
            background-color: #1e1e2f !important;
            border-color: #667eea !important;
            color: white !important;
        } */

        .form-control {
            background-color: #3b3b3b !important;
            border: 1px solid #555 !important;
            color: white !important;
        }

        .form-control:focus {
            background-color: #444 !important;
            border-color: #667eea !important;
            color: white !important;
        }

        .input-group-text {
            background-color: #667eea !important;
            border-color: #667eea !important;
            color: white !important;
        }

        .btn-primary {
            background-color: #667eea !important;
            border-color: #667eea !important;
        }

        .btn-primary:hover {
            background-color: #5a67d8 !important;
            border-color: #5a67d8 !important;
        }

        /* Links */
        a {
            color: #667eea !important;
        }

        a:hover {
            color: #5a67d8 !important;
        }
    </style>
@stop



@php
    $loginUrl = View::getSection('login_url') ?? config('adminlte.login_url', 'login');
    $registerUrl = View::getSection('register_url') ?? config('adminlte.register_url', 'register');
    $passResetUrl = View::getSection('password_reset_url') ?? config('adminlte.password_reset_url', 'password/reset');

    if (config('adminlte.use_route_url', false)) {
        $loginUrl = $loginUrl ? route($loginUrl) : '';
        $registerUrl = $registerUrl ? route($registerUrl) : '';
        $passResetUrl = $passResetUrl ? route($passResetUrl) : '';
    } else {
        $loginUrl = $loginUrl ? url($loginUrl) : '';
        $registerUrl = $registerUrl ? url($registerUrl) : '';
        $passResetUrl = $passResetUrl ? url($passResetUrl) : '';
    }
@endphp

@section('auth_header', __('adminlte::adminlte.login_message'))


@section('auth_body')
    <form action="{{ $loginUrl }}" method="post">
        @csrf

        {{-- Email field --}}
        <div class="input-group mb-3">
            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email') }}" placeholder="{{ __('adminlte::adminlte.email') }}" autofocus>

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-envelope {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>

            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Password field --}}
        <div class="input-group mb-3">
            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                placeholder="{{ __('adminlte::adminlte.password') }}">

            <div class="input-group-append">
                <div class="input-group-text">
                    <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
                </div>
            </div>

            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        {{-- Login field --}}
        <div class="row">
            <div class="col-7">
                <div class="icheck-primary" title="{{ __('adminlte::adminlte.remember_me_hint') }}">
                    <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                    <label for="remember">
                        {{ __('adminlte::adminlte.remember_me') }}
                    </label>
                </div>
            </div>

            <div class="col-5">
                <button type=submit class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
                    <span class="fas fa-sign-in-alt"></span>
                    {{ __('adminlte::adminlte.sign_in') }}
                </button>
            </div>
        </div>
    </form>
@stop

@section('auth_footer')
    {{-- Password reset link --}}
    @if($passResetUrl)
        <p class="my-0">
            <a href="{{ $passResetUrl }}">
                {{ __('adminlte::adminlte.i_forgot_my_password') }}
            </a>
        </p>
    @endif

    {{-- Register link --}}
    @if($registerUrl)
        <p class="my-0">
            <a href="{{ $registerUrl }}">
                {{ __('adminlte::adminlte.register_a_new_membership') }}
            </a>
        </p>
    @endif
@stop
