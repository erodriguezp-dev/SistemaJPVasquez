@extends('layouts.app')

@section('title', 'Pedido ' . $pedido->codigo . ' - ' . config('app.name'))

@section('content')
@php
    $secuencia = ['recibido', 'en_proceso', 'terminado', 'entregado', 'confirmado'];
    $idxActual = array_search($pedido->estado, $secuencia);
    $siguienteEstado = $secuencia[$idxActual + 1] ?? null;
    $anteriorEstado = $secuencia[$idxActual - 1] ?? null;
    $rolUser = auth()->user()->rol;
    $badgeCls = match($pedido->estado) {
        'recibido' => 'bg-secondary-subtle text-secondary border-secondary-subtle',
        'en_proceso' => 'bg-primary-subtle text-primary border-primary-subtle',
        'terminado' => 'bg-info-subtle text-info border-info-subtle',
        'entregado' => 'bg-warning-subtle text-warning-emphasis border-warning-subtle',
        'confirmado' => 'bg-success-subtle text-success border-success-subtle',
        default => 'bg-light text-dark',
    };
@endphp

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h3 class="fw-bold text-dark mb-0"><i class="bi bi-file-earmark-text me-2 text-primary"></i>Pedido {{ $pedido->codigo }}</h3>
            <span class="badge {{ $badgeCls }} border fs-6 px-3 py-1 text-capitalize">{{ str_replace('_', ' ', $pedido->estado) }}</span>
        </div>
        <p class="text-secondary small mb-0">Registrado el {{ $pedido->fecha_pedido?->format('d/m/Y H:i') }} por <strong>{{ $pedido->user->name }}</strong></p>
    </div>
    <div class="d-flex gap-2">
        @if(in_array($rolUser, ['administrador', 'gerente_proyecto']))
            <a href="{{ route('pedidos.edit', $pedido) }}" class="btn btn-outline-primary shadow-sm"><i class="bi bi-pencil me-1"></i> Editar Cliente</a>
        @endif
        <a href="{{ route('pedidos.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Volver</a>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show small mb-4"><i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card border-0 shadow-sm rounded-3 mb-4 bg-light">
    <div class="card-body p-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
        <span class="fw-bold text-dark small"><i class="bi bi-diagram-3 me-1 text-primary"></i> Cambiar Estado (Actual: <strong class="text-capitalize">{{ str_replace('_', ' ', $pedido->estado) }}</strong>)</span>
        <div class="d-flex gap-2 align-items-center flex-wrap">
            @if($siguienteEstado && in_array($rolUser, ['administrador', 'gerente_proyecto', 'colaborador']))
                <form method="POST" action="{{ route('pedidos.cambiar-estado', $pedido) }}" class="d-inline-flex gap-1 align-items-center">
                    @csrf
                    <input type="hidden" name="nuevo_estado" value="{{ $siguienteEstado }}"><input type="hidden" name="es_retroceso" value="0">
                    <input type="text" name="observacion" class="form-control form-control-sm" placeholder="Obs. opcional...">
                    <button type="submit" class="btn btn-success btn-sm fw-bold px-3" onclick="return confirm('¿Avanzar estado?');">Avanzar a {{ str_replace('_', ' ', $siguienteEstado) }} <i class="bi bi-arrow-right ms-1"></i></button>
                </form>
            @endif
            @if($anteriorEstado && $rolUser === 'administrador')
                <button type="button" class="btn btn-outline-danger btn-sm fw-bold" data-bs-toggle="collapse" data-bs-target="#collapseRetroceso"><i class="bi bi-arrow-left me-1"></i> Retroceder Estado</button>
            @endif
        </div>
    </div>
    @if($anteriorEstado && $rolUser === 'administrador')
        <div class="collapse p-3 border-top bg-white" id="collapseRetroceso">
            <form method="POST" action="{{ route('pedidos.cambiar-estado', $pedido) }}" class="row g-2 align-items-center">
                @csrf
                <input type="hidden" name="nuevo_estado" value="{{ $anteriorEstado }}"><input type="hidden" name="es_retroceso" value="1">
                <div class="col-md-8"><input type="text" name="observacion" class="form-control form-control-sm" placeholder="Motivo/Observación obligatoria..." required></div>
                <div class="col-md-4"><button type="submit" class="btn btn-danger btn-sm w-100 fw-bold">Confirmar Retroceso a {{ str_replace('_', ' ', $anteriorEstado) }}</button></div>
            </form>
        </div>
    @endif
</div>

<div class="row g-4 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 fw-bold"><i class="bi bi-person-vcard text-primary me-2"></i>Datos del Cliente</div>
            <div class="card-body p-3 small">
                <p class="mb-2"><strong>Cliente:</strong> {{ $pedido->cliente_nombre }}</p>
                <p class="mb-2"><strong>Teléfono:</strong> {{ $pedido->cliente_telefono ?? '-' }}</p>
                <p class="mb-2"><strong>Dirección:</strong> {{ $pedido->cliente_direccion ?? '-' }}</p>
                <p class="mb-2"><strong>Tipo:</strong> <span class="badge bg-light text-dark border text-capitalize">{{ $pedido->tipo }}</span></p>
                <p class="mb-2"><strong>Fecha Entrega Est.:</strong> {{ $pedido->fecha_entrega?->format('d/m/Y') ?? 'No definida' }}</p>
                <p class="mb-0"><strong>Observaciones:</strong> {{ $pedido->observaciones ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="col-md-8">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 fw-bold"><i class="bi bi-list-check text-primary me-2"></i>Líneas del Pedido</div>
            <div class="table-responsive">
                <table class="table align-middle mb-0 small">
                    <thead class="table-light"><tr><th class="ps-3">Descripción / Producto</th><th class="text-center">Cant.</th><th class="text-end">P. Unit.</th><th class="text-end pe-3">Subtotal</th></tr></thead>
                    <tbody>
                        @php $totalGral = 0; @endphp
                        @foreach($pedido->detalles as $det)
                            @php $sub = $det->precio_unitario ? ($det->cantidad * $det->precio_unitario) : null; if ($sub) $totalGral += $sub; @endphp
                            <tr>
                                <td class="ps-3"><div class="fw-bold text-dark">{{ $det->descripcion }}</div>@if($det->producto)<small class="text-primary"><i class="bi bi-box-seam me-1"></i>Prod: {{ $det->producto->codigo }} - {{ $det->producto->nombre }}</small>@endif</td>
                                <td class="text-center fw-bold">{{ number_format($det->cantidad, 2) }}</td>
                                <td class="text-end">{{ $det->precio_unitario ? 'S/ ' . number_format($det->precio_unitario, 2) : '-' }}</td>
                                <td class="text-end pe-3 fw-bold">{{ $sub ? 'S/ ' . number_format($sub, 2) : '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    @if($totalGral > 0)
                        <tfoot class="table-light fw-bold"><tr><td colspan="3" class="text-end ps-3">Monto Estimado Total:</td><td class="text-end pe-3 text-primary fs-6">S/ {{ number_format($totalGral, 2) }}</td></tr></tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Historial de Trazabilidad</div>
            <div class="card-body p-3">
                <ul class="list-group list-group-flush small">
                    @foreach($pedido->historialEstados as $hist)
                        <li class="list-group-item px-0 py-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <div><span class="badge bg-secondary-subtle text-secondary border me-1">{{ $hist->estado_anterior ? str_replace('_', ' ', $hist->estado_anterior) : 'Inicio' }}</span><i class="bi bi-arrow-right text-muted me-1"></i><span class="badge bg-primary-subtle text-primary border me-1">{{ str_replace('_', ' ', $hist->estado_nuevo) }}</span></div>
                                <small class="text-muted">{{ $hist->fecha_cambio?->format('d/m/Y H:i') }}</small>
                            </div>
                            <div class="mt-1 text-muted">Por: <strong>{{ $hist->user->name }}</strong></div>
                            @if($hist->observacion)<div class="mt-1 fst-italic text-dark bg-light p-2 rounded"><i class="bi bi-chat-text me-1 text-secondary"></i>"{{ $hist->observacion }}"</div>@endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-header bg-white py-3 fw-bold"><i class="bi bi-boxes me-2 text-primary"></i>Movimientos de Inventario Asociados</div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light"><tr><th class="ps-3">Producto</th><th class="text-center">Tipo</th><th class="text-center">Cant.</th><th>Fecha</th></tr></thead>
                    <tbody>
                        @forelse($pedido->movimientosInventario as $mov)
                            <tr>
                                <td class="ps-3 fw-bold text-dark">{{ $mov->producto->nombre }}</td>
                                <td class="text-center"><span class="badge bg-danger-subtle text-danger border">Salida</span></td>
                                <td class="text-center fw-bold">{{ number_format($mov->cantidad, 2) }}</td>
                                <td><small class="text-muted">{{ $mov->fecha_movimiento?->format('d/m/Y H:i') }}</small></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-3 text-muted">Este pedido no generó movimientos de inventario.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
