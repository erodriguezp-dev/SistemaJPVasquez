<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Sistema JP Vásquez'))</title>
    @vite(['resources/sass/app.scss', 'resources/js/app.js'])
    <style>
        :root { --sidebar-width: 250px; }
        body { min-height: 100vh; display: flex; flex-direction: column; background-color: #f4f6f9; }
        #sidebar-wrapper { width: var(--sidebar-width); min-height: calc(100vh - 56px); transition: margin 0.25s ease-out; }
        #page-content-wrapper { flex: 1; min-width: 0; }
        .sidebar-link {
            display: flex; align-items: center; padding: 0.6rem 1rem;
            color: #495057; text-decoration: none; transition: all 0.2s;
            border-radius: 0.375rem; margin: 0.125rem 0.5rem; font-size: 0.9rem;
        }
        .sidebar-link:hover, .sidebar-link.active { background-color: #e9ecef; color: #0d6efd; font-weight: 600; }
        .sidebar-link i { font-size: 1.1rem; margin-right: 0.6rem; }
        .sidebar-heading { font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.05rem; font-weight: 700; color: #6c757d; padding: 0.75rem 1rem 0.25rem; }
        .pagination svg { width: 1rem; height: 1rem; vertical-align: middle; }
        .pagination .page-link { display: inline-flex; align-items: center; justify-content: center; }
        @media (max-width: 768px) {
            #sidebar-wrapper { margin-left: calc(-1 * var(--sidebar-width)); }
            #sidebar-wrapper.show { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
        <div class="container-fluid">
            <button class="btn btn-outline-light btn-sm me-2 d-md-none" type="button" id="sidebarToggle" aria-label="Alternar navegación">
                <i class="bi bi-list fs-5"></i>
            </button>
            <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ url('/') }}">
                <i class="bi bi-box-seam me-2 text-primary"></i> <span>{{ config('app.name') }}</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#topNavbarContent">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="topNavbarContent">
                <ul class="navbar-nav ms-auto align-items-center">
                    @auth
                        <li class="nav-item me-2">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle text-uppercase px-2 py-1">
                                {{ config('permissions.roles.'.auth()->user()->rol, auth()->user()->rol) }}
                            </span>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle text-light d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle fs-5 me-1"></i> <span class="fw-semibold">{{ auth()->user()->name }}</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li><h6 class="dropdown-header">Usuario: {{ auth()->user()->usuario }}</h6></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <div class="d-flex flex-grow-1">
        <aside id="sidebar-wrapper" class="bg-white border-end py-2 shadow-sm">
            <nav class="nav flex-column">
                <a href="{{ url('/') }}" class="sidebar-link {{ request()->is('/') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> <span>Inicio</span>
                </a>

                @auth
                    @php $modulosPermitidos = config('permissions.modules', []); $r = auth()->user()->rol; @endphp

                    @if(in_array($r, $modulosPermitidos['usuarios']['ver'] ?? []))
                        <div class="sidebar-heading">Seguridad</div>
                        <a href="{{ route('usuarios.index') }}" class="sidebar-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}">
                            <i class="bi bi-people"></i> <span>Usuarios</span>
                        </a>
                    @endif

                    @if(in_array($r, $modulosPermitidos['inventario']['ver'] ?? []))
                        <div class="sidebar-heading">Gestión de Inventario</div>
                        <a href="{{ route('categorias.index') }}" class="sidebar-link {{ request()->routeIs('categorias.*') ? 'active' : '' }}">
                            <i class="bi bi-tags"></i> <span>Categorías</span>
                        </a>
                        <a href="{{ route('productos.index') }}" class="sidebar-link {{ request()->routeIs('productos.*') ? 'active' : '' }}">
                            <i class="bi bi-box-seam"></i> <span>Catálogo de Productos</span>
                        </a>
                        <a href="{{ route('inventario.stock') }}" class="sidebar-link {{ request()->is('inventario*') ? 'active' : '' }}">
                            <i class="bi bi-boxes"></i> <span>Stock en Tiempo Real</span>
                        </a>
                        <a href="{{ route('movimientos.index') }}" class="sidebar-link {{ request()->routeIs('movimientos.*') ? 'active' : '' }}">
                            <i class="bi bi-arrow-down-up"></i> <span>Movimientos & Kardex</span>
                        </a>
                    @endif

                    @if(in_array($r, $modulosPermitidos['pedidos']['ver'] ?? []))
                        <div class="sidebar-heading">Gestión de Pedidos</div>
                        <a href="{{ route('pedidos.index') }}" class="sidebar-link {{ request()->routeIs('pedidos.*') ? 'active' : '' }}">
                            <i class="bi bi-cart-check"></i> <span>Pedidos de Clientes</span>
                        </a>
                    @endif

                    @if(in_array($r, $modulosPermitidos['reportes']['ver'] ?? []))
                        <div class="sidebar-heading">Reportes y Auditoría</div>
                        <a href="{{ route('reportes.dashboard') }}" class="sidebar-link {{ request()->routeIs('reportes.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-speedometer2"></i> <span>Dashboard General</span>
                        </a>
                        <a href="{{ route('reportes.index') }}" class="sidebar-link {{ request()->routeIs('reportes.index') ? 'active' : '' }}">
                            <i class="bi bi-file-earmark-bar-graph"></i> <span>Generar Reportes</span>
                        </a>
                        <a href="{{ route('reportes.historial') }}" class="sidebar-link {{ request()->routeIs('reportes.historial*') ? 'active' : '' }}">
                            <i class="bi bi-clock-history"></i> <span>Historial de Reportes</span>
                        </a>
                    @endif
                @endauth
            </nav>
        </aside>

        <main id="page-content-wrapper" class="p-4">
            <div class="container-fluid">
                @yield('content')
            </div>
        </main>
    </div>

    <footer class="bg-white border-top py-3 mt-auto text-center text-muted small">
        <div class="container-fluid">&copy; {{ date('Y') }} {{ config('app.name') }} &mdash; Todos los derechos reservados.</div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar-wrapper');
            if (sidebarToggle && sidebar) {
                sidebarToggle.addEventListener('click', () => sidebar.classList.toggle('show'));
            }

            document.querySelectorAll('.toggle-password').forEach(button => {
                button.addEventListener('click', function () {
                    const targetId = this.getAttribute('data-target');
                    const input = document.getElementById(targetId);
                    const icon = this.querySelector('i');
                    if (input && icon) {
                        if (input.type === 'password') {
                            input.type = 'text';
                            icon.classList.replace('bi-eye', 'bi-eye-slash');
                        } else {
                            input.type = 'password';
                            icon.classList.replace('bi-eye-slash', 'bi-eye');
                        }
                    }
                });
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
