@extends('layouts.app')

@section('title', 'Movimientos de Inventario - ' . config('app.name'))

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold text-dark mb-1"><i class="bi bi-arrow-down-up me-2 text-primary"></i>Movimientos de Inventario</h3>
        <p class="text-secondary small mb-0">Historial general de entradas, salidas y ajustes de insumos.</p>
    </div>
    @if(in_array(auth()->user()->rol, ['administrador', 'colaborador']))
        <div class="d-flex gap-2">
            <a href="{{ route('inventario.ajuste.create') }}" class="btn btn-outline-warning fw-bold shadow-sm">
                <i class="bi bi-sliders me-1"></i> Ajuste Físico
            </a>
            <a href="{{ route('movimientos.create') }}" class="btn btn-primary fw-bold shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> Registrar Entrada / Salida
            </a>
        </div>
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
        <form method="GET" action="{{ route('movimientos.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <select name="producto_id" class="form-select">
                    <option value="">-- Todos los productos --</option>
                    @foreach ($productos as $p)
                        <option value="{{ $p->id }}" {{ (string)$productoId === (string)$p->id ? 'selected' : '' }}>{{ $p->codigo }} - {{ $p->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="tipo" class="form-select">
                    <option value="">-- Tipo --</option>
                    <option value="entrada" {{ $tipo === 'entrada' ? 'selected' : '' }}>Entrada</option>
                    <option value="salida" {{ $tipo === 'salida' ? 'selected' : '' }}>Salida</option>
                    <option value="ajuste" {{ $tipo === 'ajuste' ? 'selected' : '' }}>Ajuste</option>
                </select>
            </div>
            <div class="col-md-2">
                <input type="date" name="fecha_inicio" class="form-control" value="{{ $fechaInicio }}" placeholder="Fecha inicio">
            </div>
            <div class="col-md-2">
                <input type="date" name="fecha_fin" class="form-control" value="{{ $fechaFin }}" placeholder="Fecha fin">
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-secondary w-100 fw-bold">Filtrar</button>
                @if($productoId || $tipo || $fechaInicio || $fechaFin)
                    <a href="{{ route('movimientos.index') }}" class="btn btn-outline-secondary" title="Limpiar"><i class="bi bi-x-circle"></i></a>
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
                    <th class="ps-3">Fecha y Hora</th>
                    <th>Producto</th>
                    <th class="text-center">Tipo</th>
                    <th class="text-end">Cantidad</th>
                    <th class="text-center">Stock (Ant. &rarr; Res.)</th>
                    <th>Motivo</th>
                    <th>Registrado por</th>
                    <th class="pe-3">Referencia</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($movimientos as $mov)
                    <tr>
                        <td class="ps-3 small text-secondary fw-semibold">{{ $mov->fecha_movimiento->format('d/m/Y H:i') }}</td>
                        <td>
                            <a href="{{ route('inventario.kardex', $mov->producto) }}" class="fw-bold text-decoration-none text-dark">
                                <code>{{ $mov->producto->codigo }}</code> {{ $mov->producto->nombre }}
                            </a>
                        </td>
                        <td class="text-center">
                            @if($mov->tipo === 'entrada')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="bi bi-arrow-down-left me-1"></i>Entrada</span>
                            @elseif($mov->tipo === 'salida')
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><i class="bi bi-arrow-up-right me-1"></i>Salida</span>
                            @else
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1"><i class="bi bi-sliders me-1"></i>Ajuste</span>
                            @endif
                        </td>
                        <td class="text-end fw-bold">
                            @if($mov->tipo === 'salida')
                                <span class="text-danger">-{{ number_format($mov->cantidad, 2) }}</span>
                            @elseif($mov->tipo === 'entrada')
                                <span class="text-success">+{{ number_format($mov->cantidad, 2) }}</span>
                            @else
                                <span class="text-info">{{ number_format($mov->cantidad, 2) }}</span>
                            @endif
                            <small class="text-muted fw-normal">{{ $mov->producto->unidad_medida }}</small>
                        </td>
                        <td class="text-center small">
                            <span class="text-muted">{{ number_format($mov->stock_anterior, 2) }}</span> &rarr;
                            <span class="fw-bold text-dark">{{ number_format($mov->stock_resultante, 2) }}</span>
                        </td>
                        <td class="small text-secondary">{{ $mov->motivo ?? '-' }}</td>
                        <td class="small fw-semibold">{{ $mov->user->name }}</td>
                        <td class="pe-3 small text-muted">
                            @if($mov->pedido)
                                <span class="badge bg-light text-dark border">Pedido {{ $mov->pedido->codigo }}</span>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center py-4 text-muted">No se encontraron movimientos de inventario.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($movimientos->hasPages())
        <div class="card-footer bg-white border-top py-3">{{ $movimientos->links() }}</div>
    @endif
</div>
@endsection
