@extends('layouts.app')

@section('title', 'Editar Pedido ' . $pedido->codigo . ' - ' . config('app.name'))

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1"><i class="bi bi-pencil-square me-2 text-primary"></i>Editar Pedido {{ $pedido->codigo }}</h3>
        <p class="text-secondary small mb-0">Modificación de información del cliente, fechas de entrega y ajuste de cantidades.</p>
    </div>
    <a href="{{ route('pedidos.show', $pedido) }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Volver al detalle</a>
</div>

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show small mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="alert alert-info border-info-subtle small mb-4">
    <i class="bi bi-info-circle-fill me-1"></i>
    <strong>Ajuste Automático de Inventario:</strong> Si modifica la cantidad de un ítem con producto de inventario, el sistema calculará la diferencia y registrará automáticamente la salida adicional o la devolución al stock.
</div>

<form method="POST" action="{{ route('pedidos.update', $pedido) }}">
    @csrf
    @method('PUT')

    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 fw-bold"><i class="bi bi-person-vcard text-primary me-2"></i>Datos del Cliente y Entrega</div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label small fw-bold">Nombre del Cliente <span class="text-danger">*</span></label>
                    <input type="text" name="cliente_nombre" class="form-control @error('cliente_nombre') is-invalid @enderror" value="{{ old('cliente_nombre', $pedido->cliente_nombre) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Teléfono de Contacto (9 dígitos)</label>
                    <input type="text" name="cliente_telefono" class="form-control @error('cliente_telefono') is-invalid @enderror" value="{{ old('cliente_telefono', $pedido->cliente_telefono) }}" placeholder="Ej. 987654321" maxlength="9" pattern="[0-9]{9}" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,9)">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Dirección de Entrega</label>
                    <input type="text" name="cliente_direccion" class="form-control" value="{{ old('cliente_direccion', $pedido->cliente_direccion) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Fecha de Entrega Estimada</label>
                    <input type="date" name="fecha_entrega" class="form-control" value="{{ old('fecha_entrega', $pedido->fecha_entrega?->format('Y-m-d')) }}">
                </div>
                <div class="col-12">
                    <label class="form-label small fw-bold">Observaciones Generales</label>
                    <textarea name="observaciones" class="form-control" rows="2">{{ old('observaciones', $pedido->observaciones) }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 fw-bold text-dark"><i class="bi bi-pencil text-primary me-2"></i>Ajuste de Cantidades y Precios de Ítems</div>
        <div class="table-responsive">
            <table class="table align-middle mb-0 small">
                <thead class="table-light">
                    <tr>
                        <th class="ps-3" style="width: 45%;">Descripción / Producto</th>
                        <th style="width: 25%;">Cantidad <span class="text-danger">*</span></th>
                        <th style="width: 30%;" class="pe-3">Precio Unitario (S/)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pedido->detalles as $idx => $det)
                        <tr>
                            <td class="ps-3">
                                <input type="hidden" name="detalles[{{ $idx }}][id]" value="{{ $det->id }}">
                                <div class="fw-bold text-dark">{{ $det->descripcion }}</div>
                                @if($det->producto)
                                    <small class="text-primary"><i class="bi bi-box-seam me-1"></i>Prod: {{ $det->producto->codigo }} - {{ $det->producto->nombre }} (Stock Disp: {{ $det->producto->stock_actual }})</small>
                                @else
                                    <small class="text-muted"><i class="bi bi-tools me-1"></i>Servicio / Sin Producto de Almacén</small>
                                @endif
                            </td>
                            <td>
                                <input type="number" step="0.01" min="0.01" name="detalles[{{ $idx }}][cantidad]" class="form-control form-control-sm @error("detalles.{$idx}.cantidad") is-invalid @enderror" value="{{ old("detalles.{$idx}.cantidad", $det->cantidad) }}" required>
                            </td>
                            <td class="pe-3">
                                <input type="number" step="0.01" min="0" name="detalles[{{ $idx }}][precio_unitario]" class="form-control form-control-sm" value="{{ old("detalles.{$idx}.precio_unitario", $det->precio_unitario) }}" placeholder="0.00">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route('pedidos.show', $pedido) }}" class="btn btn-secondary px-4">Cancelar</a>
        <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm"><i class="bi bi-save me-1"></i> Guardar Cambios</button>
    </div>
</form>
@endsection

