@extends('layouts.app')

@section('title', 'Registrar Movimiento - ' . config('app.name'))

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h3 class="fw-bold text-dark mb-0">Registrar Entrada / Salida de Inventario</h3>
            <a href="{{ route('movimientos.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Volver a movimientos
            </a>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show small mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ $errors->first() }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('movimientos.store') }}" novalidate>
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-8">
                            <label for="producto_id" class="form-label fw-bold small text-secondary">Producto / Insumo *</label>
                            <select class="form-select @error('producto_id') is-invalid @enderror" id="producto_id" name="producto_id" required>
                                <option value="">-- Seleccionar Producto --</option>
                                @foreach ($productos as $prod)
                                    <option value="{{ $prod->id }}" {{ (string)$productoSeleccionadoId === (string)$prod->id || old('producto_id') == $prod->id ? 'selected' : '' }}>
                                        {{ $prod->codigo }} - {{ $prod->nombre }} (Stock actual: {{ number_format($prod->stock_actual, 2) }} {{ $prod->unidad_medida }})
                                    </option>
                                @endforeach
                            </select>
                            @error('producto_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="tipo" class="form-label fw-bold small text-secondary">Tipo de Movimiento *</label>
                            <select class="form-select @error('tipo') is-invalid @enderror" id="tipo" name="tipo" required>
                                <option value="entrada" {{ old('tipo') === 'entrada' ? 'selected' : '' }}>Entrada (Ingreso)</option>
                                <option value="salida" {{ old('tipo') === 'salida' ? 'selected' : '' }}>Salida (Despacho / Consumo)</option>
                            </select>
                            @error('tipo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="cantidad" class="form-label fw-bold small text-secondary">Cantidad *</label>
                            <input type="number" step="0.01" class="form-control @error('cantidad') is-invalid @enderror" id="cantidad" name="cantidad" value="{{ old('cantidad') }}" placeholder="Mayor a cero" required>
                            @error('cantidad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="fecha_movimiento" class="form-label fw-bold small text-secondary">Fecha del Movimiento (Opcional)</label>
                            <input type="datetime-local" class="form-control @error('fecha_movimiento') is-invalid @enderror" id="fecha_movimiento" name="fecha_movimiento" value="{{ old('fecha_movimiento') }}">
                            @error('fecha_movimiento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label for="motivo" class="form-label fw-bold small text-secondary">Motivo o Justificación *</label>
                            <input type="text" class="form-control @error('motivo') is-invalid @enderror" id="motivo" name="motivo" value="{{ old('motivo') }}" placeholder="Ej. Compra a proveedor, Consumo interno de taller, Devolución" required max="255">
                            @error('motivo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                        <a href="{{ route('movimientos.index') }}" class="btn btn-light px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">
                            <i class="bi bi-check-circle me-1"></i> Registrar Movimiento
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
