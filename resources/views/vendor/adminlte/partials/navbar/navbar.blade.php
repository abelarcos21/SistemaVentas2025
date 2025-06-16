<nav class="main-header navbar navbar-expand navbar-light bg-white border-bottom">
    <!-- Botón de menú -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ route('home') }}" class="nav-link">Inicio</a>
        </li>
    </ul>

    <!-- Usuario -->
    <ul class="navbar-nav ml-auto">
        <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#">
                <i class="fas fa-user-circle"></i> {{ Auth::user()->name ?? 'Invitado' }}
            </a>
            <div class="dropdown-menu dropdown-menu-right">
                <a href="{{ route('configuracion.perfil') }}" class="dropdown-item"><i class="fas fa-cog"></i> Perfil</a>
                <div class="dropdown-divider"></div>
                <form action="{{ route('logout') }}" method="POST">@csrf
                    <button class="dropdown-item"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</button>
                </form>
            </div>
        </li>
    </ul>
</nav>