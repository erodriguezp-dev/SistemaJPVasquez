@extends('layouts.app')

@section('title', 'Gestión de Usuarios - ' . config('app.name'))

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold text-dark mb-1">
            <i class="bi bi-people me-2 text-primary"></i>Gestión de Usuarios
        </h3>
        <p class="text-secondary small mb-0">Administración de usuarios, asignación de roles y control de acceso.</p>
    </div>
    <div>
        <a href="{{ route('usuarios.create') }}" class="btn btn-primary fw-bold shadow-sm">
            <i class="bi bi-person-plus-fill me-1"></i> Registrar Usuario
        </a>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show small mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show small mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('usuarios.index') }}" class="row g-2 align-items-center">
            <div class="col-md-6 col-lg-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="buscar" class="form-control border-start-0" placeholder="Buscar por nombre, usuario o email..." value="{{ $search }}">
                </div>
            </div>
            <div class="col-md-4 col-lg-3">
                <select name="rol" class="form-select">
                    <option value="">-- Todos los roles --</option>
                    @foreach (config('permissions.roles', []) as $key => $label)
                        <option value="{{ $key }}" {{ $rol === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 col-lg-4 d-flex gap-2">
                <button type="submit" class="btn btn-secondary fw-bold px-3">Filtrar</button>
                @if($search || $rol)
                    <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary" title="Limpiar filtro">
                        <i class="bi bi-x-circle"></i> Limpiar
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Nombre</th>
                    <th>Usuario</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th class="text-center">Estado</th>
                    <th class="text-end pe-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($usuarios as $usr)
                    <tr>
                        <td class="ps-3 fw-bold text-dark">
                            {{ $usr->name }}
                            @if(auth()->id() === $usr->id)
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill ms-1 small">Tú</span>
                            @endif
                        </td>
                        <td><code>{{ $usr->usuario }}</code></td>
                        <td>{{ $usr->email ?? '-' }}</td>
                        <td>
                            @php
                                $badgeColor = match($usr->rol) {
                                    'administrador' => 'bg-danger-subtle text-danger border-danger-subtle',
                                    'gerente_proyecto' => 'bg-primary-subtle text-primary border-primary-subtle',
                                    'contador' => 'bg-info-subtle text-info border-info-subtle',
                                    default => 'bg-secondary-subtle text-secondary border-secondary-subtle',
                                };
                            @endphp
                            <span class="badge {{ $badgeColor }} border px-2 py-1">
                                {{ config('permissions.roles.'.$usr->rol, $usr->rol) }}
                            </span>
                        </td>
                        <td class="text-center">
                            @if ($usr->activo)
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Activo</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Inactivo</span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('usuarios.edit', $usr) }}" class="btn btn-outline-primary" title="Editar usuario">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                @if(auth()->id() !== $usr->id)
                                    <form method="POST" action="{{ route('usuarios.toggle-status', $usr) }}" class="d-inline" onsubmit="return confirm('¿Está seguro de cambiar el estado de este usuario?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn {{ $usr->activo ? 'btn-outline-danger' : 'btn-outline-success' }}" title="{{ $usr->activo ? 'Desactivar usuario' : 'Activar usuario' }}">
                                            <i class="bi {{ $usr->activo ? 'bi-person-x' : 'bi-person-check' }}"></i>
                                        </button>
                                    </form>
                                @else
                                    <button class="btn btn-outline-secondary" disabled title="No puede desactivar su propia cuenta">
                                        <i class="bi bi-lock-fill"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                            No se encontraron usuarios registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($usuarios->hasPages())
        <div class="card-footer bg-white border-top py-3">
            {{ $usuarios->links() }}
        </div>
    @endif
</div>
@endsection
