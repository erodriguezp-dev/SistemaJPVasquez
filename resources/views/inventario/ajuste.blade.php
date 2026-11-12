@extends('layouts.app')

@section('title', 'Ajuste de Inventario - ' . config('app.name'))

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h3 class="fw-bold text-dark mb-0"><i class="bi bi-sliders me-2 text-warning"></i>Ajuste de Stock por Conteo Físico</h3>
            <a href="{{ route('inventario.stock') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Volver a Stock
            </a>
        </div>

        @if (session('warning'))
            <div class="alert alert-warning alert-dismissible fade show small mb-4" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('inventario.ajuste.store') }}" novalidate>
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-7">
                            <label for="producto_id" class="form-label fw-bold small text-secondary">Producto a Ajustar *</label>
                            <select class="form-select @error('producto_id') is-invalid @enderror" id="producto_id" name="producto_id" required>
                                <option value="">-- Seleccionar Producto --</option>
                                @foreach ($productos as $prod)
                                    <option value="{{ $prod->id }}" data-stock="{{ $prod->stock_actual }}" data-unidad="{{ $prod->unidad_medida }}" {{ (string)$productoSeleccionadoId === (string)$prod->id || old('producto_id') == $prod->id ? 'selected' : '' }}>
                                        {{ $prod->codigo }} - {{ $prod->nombre }} (Stock en sistema: {{ number_format($prod->stock_actual, 2) }} {{ $prod->unidad_medida }})
                                    </option>
                                @endforeach
                            </select>
                            @error('producto_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-5">
                            <label for="stock_fisico" class="form-label fw-bold small text-secondary">Stock Físico Contado *</label>
                            <input type="number" step="0.01" class="form-control @error('stock_fisico') is-invalid @enderror" id="stock_fisico" name="stock_fisico" value="{{ old('stock_fisico') }}" placeholder="Ej. 15.00" required>
                            @error('stock_fisico') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-12">
                            <label for="motivo" class="form-label fw-bold small text-secondary">Motivo Obligatorio del Ajuste *</label>
                            <input type="text" class="form-control @error('motivo') is-invalid @enderror" id="motivo" name="motivo" value="{{ old('motivo') }}" placeholder="Ej. Conteo físico de inventario anual, Merma por producto dañado, Corrección de saldo" required max="255">
                            @error('motivo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="alert alert-light border small mt-4 mb-0">
                        <i class="bi bi-info-circle text-primary me-1"></i> El sistema calculará la diferencia entre el <strong>stock en sistema</strong> y el <strong>stock físico contado</strong>. La diferencia se registrará automáticamente como un movimiento de tipo <code>ajuste</code> a su nombre.
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                        <a href="{{ route('inventario.stock') }}" class="btn btn-light px-4">Cancelar</a>
                        <button type="submit" class="btn btn-warning px-4 fw-bold">
                            <i class="bi bi-check-lg me-1"></i> Aplicar Ajuste
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
