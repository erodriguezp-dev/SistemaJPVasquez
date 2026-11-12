@extends('layouts.app')

@section('title', 'Gestión de Pedidos - ' . config('app.name'))

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold text-dark mb-1"><i class="bi bi-cart-check me-2 text-primary"></i>Gestión de Pedidos</h3>
        <p class="text-secondary small mb-0">Registro, seguimiento de estados y trazabilidad de pedidos.</p>
    </div>
    @if(in_array(auth()->user()->rol, ['administrador', 'gerente_proyecto', 'colaborador']))
        <a href="{{ route('pedidos.create') }}" class="btn btn-primary fw-bold shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Registrar Pedido
        </a>
    @endif
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show small mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('pedidos.index') }}" class="row g-2 align-items-center">
            <div class="col-md-3">
                <input type="text" name="buscar" class="form-control" placeholder="Buscar por código o cliente..." value="{{ $buscar }}">
            </div>
            <div class="col-md-2">
                <select name="tipo" class="form-select">
                    <option value="">-- Tipo --</option>
                    <option value="mueble" {{ $tipo === 'mueble' ? 'selected' : '' }}>Mueble</option>
                    <option value="ferreteria" {{ $tipo === 'ferreteria' ? 'selected' : '' }}>Ferretería</option>
                    <option value="reparacion" {{ $tipo === 'reparacion' ? 'selected' : '' }}>Reparación</option>
                </select>
            </div>
            <div class="col-md-2">
                <select name="estado" class="form-select">
                    <option value="">-- Estado --</option>
                    <option value="recibido" {{ $estado === 'recibido' ? 'selected' : '' }}>Recibido</option>
                    <option value="en_proceso" {{ $estado === 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                    <option value="terminado" {{ $estado === 'terminado' ? 'selected' : '' }}>Terminado</option>
                    <option value="entregado" {{ $estado === 'entregado' ? 'selected' : '' }}>Entregado</option>
                    <option value="confirmado" {{ $estado === 'confirmado' ? 'selected' : '' }}>Confirmado</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="fecha_inicio" class="form-control" title="Fecha inicio" value="{{ $fechaInicio }}">
            </div>
            <div class="col-md-2">
                <input type="date" name="fecha_fin" class="form-control" title="Fecha fin" value="{{ $fechaFin }}">
            </div>
            <div class="col-md-1 d-flex">
                <button type="submit" class="btn btn-secondary w-100"><i class="bi bi-search"></i></button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Código</th>
                    <th>Tipo</th>
                    <th>Cliente</th>
                    <th>Fecha Pedido</th>
                    <th>Fecha Entrega</th>
                    <th class="text-center">Estado</th>
                    <th>Registrado Por</th>
                    <th class="text-end pe-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($pedidos as $ped)
                    <tr>
                        <td class="ps-3"><a href="{{ route('pedidos.show', $ped) }}" class="fw-bold text-decoration-none"><code>{{ $ped->codigo }}</code></a></td>
                        <td>
                            @php
                                $tipoColor = match($ped->tipo) {
                                    'mueble' => 'bg-primary-subtle text-primary border-primary-subtle',
                                    'ferreteria' => 'bg-info-subtle text-info border-info-subtle',
                                    'reparacion' => 'bg-warning-subtle text-warning-emphasis border-warning-subtle',
                                    default => 'bg-light text-dark',
                                };
                            @endphp
                            <span class="badge {{ $tipoColor }} border px-2 py-1 text-capitalize">{{ $ped->tipo }}</span>
                        </td>
                        <td class="fw-semibold text-dark">{{ $ped->cliente_nombre }}</td>
                        <td>{{ $ped->fecha_pedido?->format('d/m/Y H:i') }}</td>
                        <td>{{ $ped->fecha_entrega?->format('d/m/Y') ?? '-' }}</td>
                        <td class="text-center">
                            @php
                                $estadoBadge = match($ped->estado) {
                                    'recibido' => 'bg-secondary-subtle text-secondary border-secondary-subtle',
                                    'en_proceso' => 'bg-primary-subtle text-primary border-primary-subtle',
                                    'terminado' => 'bg-info-subtle text-info border-info-subtle',
                                    'entregado' => 'bg-warning-subtle text-warning-emphasis border-warning-subtle',
                                    'confirmado' => 'bg-success-subtle text-success border-success-subtle',
                                    default => 'bg-light text-dark',
                                };
                                $estadoLabel = match($ped->estado) {
                                    'recibido' => 'Recibido',
                                    'en_proceso' => 'En Proceso',
                                    'terminado' => 'Terminado',
                                    'entregado' => 'Entregado',
                                    'confirmado' => 'Confirmado',
                                    default => $ped->estado,
                                };
                            @endphp
                            <span class="badge {{ $estadoBadge }} border px-2 py-1">{{ $estadoLabel }}</span>
                        </td>
                        <td><small class="text-muted">{{ $ped->user->name }}</small></td>
                        <td class="text-end pe-3">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('pedidos.show', $ped) }}" class="btn btn-outline-info" title="Ver detalle del pedido">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if(in_array(auth()->user()->rol, ['administrador', 'gerente_proyecto']))
                                    <a href="{{ route('pedidos.edit', $ped) }}" class="btn btn-outline-primary" title="Editar cliente / entrega">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center py-4 text-muted">No se encontraron pedidos registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($pedidos->hasPages())
        <div class="card-footer bg-white border-top py-3">{{ $pedidos->links() }}</div>
    @endif
</div>
@endsection
