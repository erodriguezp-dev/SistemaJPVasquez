@extends('layouts.app')

@section('title', 'Inicio - ' . config('app.name'))

@section('content')
@php
    $cantAlertas = \App\Models\Producto::where('activo', true)->whereColumn('stock_actual', '<=', 'stock_minimo')->count();
    $cantAgotados = \App\Models\Producto::where('activo', true)->where('stock_actual', '<=', 0)->count();

    $pedidosHoy = \App\Models\Pedido::whereDate('fecha_pedido', \Carbon\Carbon::today('America/Lima'))->count();
    $pedidosRecibidos = \App\Models\Pedido::where('estado', 'recibido')->count();
    $pedidosEnProceso = \App\Models\Pedido::where('estado', 'en_proceso')->count();
    $pedidosTerminados = \App\Models\Pedido::where('estado', 'terminado')->count();
    $pedidosEntregados = \App\Models\Pedido::where('estado', 'entregado')->count();
    $pedidosConfirmados = \App\Models\Pedido::where('estado', 'confirmado')->count();
@endphp

<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div>
                        <h2 class="h4 fw-bold text-dark mb-1">Bienvenido, {{ auth()->user()->name }}</h2>
                        <p class="text-secondary mb-0">Sistema de Gestión de Inventario, Pedidos y Reportes - {{ config('app.name') }}</p>
                    </div>
                    <div>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 rounded-pill text-uppercase">
                            <i class="bi bi-shield-check me-1"></i> Rol: {{ config('permissions.roles.'.auth()->user()->rol) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($cantAlertas > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-3 bg-warning-subtle border-warning-subtle">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div class="d-flex align-items-center">
                            <div class="bg-warning text-dark p-3 rounded-circle me-3">
                                <i class="bi bi-exclamation-triangle-fill fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold text-dark mb-1">Alerta de Stock Mínimo y Existencias</h5>
                                <p class="text-dark mb-0 small">
                                    Se identificaron <strong>{{ $cantAlertas }} productos</strong> con stock bajo o igual al mínimo establecido ({{ $cantAgotados }} completamente agotados).
                                </p>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('inventario.stock', ['estado_stock' => 'alerta']) }}" class="btn btn-warning fw-bold px-4 shadow-sm">
                                <i class="bi bi-eye me-1"></i> Ver Productos en Alerta
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Tarjeta de Resumen de Pedidos -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                <span class="fw-bold"><i class="bi bi-cart-check text-primary me-2"></i>Estado de Pedidos de Clientes</span>
                <span class="badge bg-primary px-3 py-1">Registrados Hoy: {{ $pedidosHoy }}</span>
            </div>
            <div class="card-body p-3">
                <div class="row g-2 text-center">
                    <div class="col">
                        <div class="p-2 border rounded bg-light">
                            <div class="small text-muted fw-bold">Recibidos</div>
                            <div class="fs-4 fw-bold text-secondary">{{ $pedidosRecibidos }}</div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="p-2 border rounded bg-light">
                            <div class="small text-muted fw-bold">En Proceso</div>
                            <div class="fs-4 fw-bold text-primary">{{ $pedidosEnProceso }}</div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="p-2 border rounded bg-light">
                            <div class="small text-muted fw-bold">Terminados</div>
                            <div class="fs-4 fw-bold text-info">{{ $pedidosTerminados }}</div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="p-2 border rounded bg-light">
                            <div class="small text-muted fw-bold">Entregados</div>
                            <div class="fs-4 fw-bold text-warning-emphasis">{{ $pedidosEntregados }}</div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="p-2 border rounded bg-light">
                            <div class="small text-muted fw-bold">Confirmados</div>
                            <div class="fs-4 fw-bold text-success">{{ $pedidosConfirmados }}</div>
                        </div>
                    </div>
                </div>
                <div class="mt-3 d-flex justify-content-end gap-2">
                    @if(in_array(auth()->user()->rol, ['administrador', 'gerente_proyecto', 'colaborador']))
                        <a href="{{ route('pedidos.create') }}" class="btn btn-sm btn-primary fw-bold"><i class="bi bi-plus-lg me-1"></i> Nuevo Pedido</a>
                    @endif
                    <a href="{{ route('pedidos.index') }}" class="btn btn-sm btn-outline-primary">Ver Todos los Pedidos</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tarjetas de Acceso Rápido al Módulo de Inventario -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary-subtle text-primary p-3 rounded-3 me-3"><i class="bi bi-boxes fs-3"></i></div>
                    <div>
                        <div class="text-muted small fw-semibold">Catálogo</div>
                        <div class="fw-bold text-dark fs-5">Productos</div>
                    </div>
                </div>
                <a href="{{ route('productos.index') }}" class="btn btn-sm btn-outline-primary w-100">Ir a Productos</a>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-success-subtle text-success p-3 rounded-3 me-3"><i class="bi bi-speedometer fs-3"></i></div>
                    <div>
                        <div class="text-muted small fw-semibold">Existencias</div>
                        <div class="fw-bold text-dark fs-5">Stock en Vivo</div>
                    </div>
                </div>
                <a href="{{ route('inventario.stock') }}" class="btn btn-sm btn-outline-success w-100">Consultar Stock</a>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-info-subtle text-info p-3 rounded-3 me-3"><i class="bi bi-arrow-down-up fs-3"></i></div>
                    <div>
                        <div class="text-muted small fw-semibold">Trazabilidad</div>
                        <div class="fw-bold text-dark fs-5">Movimientos</div>
                    </div>
                </div>
                <a href="{{ route('movimientos.index') }}" class="btn btn-sm btn-outline-info w-100">Ver Movimientos</a>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body p-3">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-secondary-subtle text-secondary p-3 rounded-3 me-3"><i class="bi bi-tags fs-3"></i></div>
                    <div>
                        <div class="text-muted small fw-semibold">Clasificación</div>
                        <div class="fw-bold text-dark fs-5">Categorías</div>
                    </div>
                </div>
                <a href="{{ route('categorias.index') }}" class="btn btn-sm btn-outline-secondary w-100">Ver Categorías</a>
            </div>
        </div>
    </div>
</div>
@endsection

