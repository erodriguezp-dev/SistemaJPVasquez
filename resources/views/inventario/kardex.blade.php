@extends('layouts.app')

@section('title', 'Kardex - ' . $producto->nombre . ' - ' . config('app.name'))

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold text-dark mb-1"><i class="bi bi-journal-text me-2 text-primary"></i>Kardex de Producto</h3>
        <p class="text-secondary small mb-0">Historial completo de trazabilidad de entradas, salidas y ajustes.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('inventario.stock') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Volver a Consulta de Stock
        </a>
    </div>
</div>

<!-- Tarjeta Info Producto -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <span class="badge bg-light text-dark border me-2">Categoría: {{ $producto->categoria->nombre }}</span>
                <span class="badge bg-secondary me-2">Código: {{ $producto->codigo }}</span>
                <h4 class="fw-bold text-dark mt-2 mb-1">{{ $producto->nombre }}</h4>
                <div class="small text-muted">
                    Unidad de Medida: <strong>{{ $producto->unidad_medida }}</strong> | Stock Mínimo: <strong>{{ number_format($producto->stock_minimo, 2) }}</strong>
                </div>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <div class="text-muted small">Stock Actual</div>
                <div class="display-6 fw-bold text-dark mb-1">{{ number_format($producto->stock_actual, 2) }}</div>
                @if($producto->stock_actual <= 0)
                    <span class="badge bg-danger text-white px-3 py-1 rounded-pill">Agotado</span>
                @elseif($producto->stock_actual <= $producto->stock_minimo)
                    <span class="badge bg-warning text-dark px-3 py-1 rounded-pill">Stock Bajo</span>
                @else
                    <span class="badge bg-success text-white px-3 py-1 rounded-pill">Stock Normal</span>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Historial del Kardex -->
<div class="card border-0 shadow-sm rounded-3 overflow-hidden">
    <div class="card-header bg-white py-3 border-0">
        <h5 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Historial de Movimientos (Kardex)</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Fecha y Hora</th>
                    <th class="text-center">Tipo</th>
                    <th>Motivo / Descripción</th>
                    <th class="text-end">Stock Anterior</th>
                    <th class="text-end">Cantidad</th>
                    <th class="text-end pe-3">Stock Resultante</th>
                    <th>Usuario</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($movimientos as $mov)
                    <tr>
                        <td class="ps-3 small text-secondary fw-semibold">{{ $mov->fecha_movimiento->format('d/m/Y H:i') }}</td>
                        <td class="text-center">
                            @if($mov->tipo === 'entrada')
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="bi bi-arrow-down-left me-1"></i>Entrada</span>
                            @elseif($mov->tipo === 'salida')
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><i class="bi bi-arrow-up-right me-1"></i>Salida</span>
                            @else
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1"><i class="bi bi-sliders me-1"></i>Ajuste</span>
                            @endif
                        </td>
                        <td class="small">
                            {{ $mov->motivo ?? 'Sin especificación' }}
                            @if($mov->pedido)
                                <span class="badge bg-light text-dark border ms-1">Pedido #{{ $mov->pedido->codigo }}</span>
                            @endif
                        </td>
                        <td class="text-end text-muted small">{{ number_format($mov->stock_anterior, 2) }}</td>
                        <td class="text-end fw-bold">
                            @if($mov->tipo === 'salida')
                                <span class="text-danger">-{{ number_format($mov->cantidad, 2) }}</span>
                            @elseif($mov->tipo === 'entrada')
                                <span class="text-success">+{{ number_format($mov->cantidad, 2) }}</span>
                            @else
                                <span class="text-info">{{ number_format($mov->cantidad, 2) }}</span>
                            @endif
                        </td>
                        <td class="text-end pe-3 fw-bold text-dark fs-6">{{ number_format($mov->stock_resultante, 2) }}</td>
                        <td class="small fw-semibold">{{ $mov->user->name }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">No se registran movimientos para este producto.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($movimientos->hasPages())
        <div class="card-footer bg-white border-top py-3">{{ $movimientos->links() }}</div>
    @endif
</div>
@endsection
