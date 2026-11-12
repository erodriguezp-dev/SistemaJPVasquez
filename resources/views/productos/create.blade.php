@extends('layouts.app')

@section('title', 'Registrar Producto - ' . config('app.name'))

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="d-flex align-items-center justify-content-between mb-4">
            <h3 class="fw-bold text-dark mb-0">Registrar Nuevo Producto / Insumo</h3>
            <a href="{{ route('productos.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Volver al catálogo
            </a>
        </div>

        <div class="card border-0 shadow-sm rounded-3">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('productos.store') }}" novalidate>
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="codigo" class="form-label fw-bold small text-secondary">Código del Producto *</label>
                            <input type="text" class="form-control @error('codigo') is-invalid @enderror" id="codigo" name="codigo" value="{{ old('codigo') }}" placeholder="Ej. CARP-MEL-005" required>
                            @error('codigo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-8">
                            <label for="nombre" class="form-label fw-bold small text-secondary">Nombre del Producto / Insumo *</label>
                            <input type="text" class="form-control @error('nombre') is-invalid @enderror" id="nombre" name="nombre" value="{{ old('nombre') }}" placeholder="Nombre descriptivo completo" required>
                            @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="categoria_id" class="form-label fw-bold small text-secondary">Categoría *</label>
                            <select class="form-select @error('categoria_id') is-invalid @enderror" id="categoria_id" name="categoria_id" required>
                                <option value="">-- Seleccionar Categoría --</option>
                                @foreach ($categorias as $cat)
                                    <option value="{{ $cat->id }}" {{ old('categoria_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                                @endforeach
                            </select>
                            @error('categoria_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="unidad_medida" class="form-label fw-bold small text-secondary">Unidad de Medida *</label>
                            <input type="text" class="form-control @error('unidad_medida') is-invalid @enderror" id="unidad_medida" name="unidad_medida" value="{{ old('unidad_medida') }}" placeholder="Ej. Plancha, Galón, m2, Pieza, Tubo" required>
                            @error('unidad_medida') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="stock_minimo" class="form-label fw-bold small text-secondary">Stock Mínimo (Alerta) *</label>
                            <input type="number" step="0.01" class="form-control @error('stock_minimo') is-invalid @enderror" id="stock_minimo" name="stock_minimo" value="{{ old('stock_minimo', '5.00') }}" required>
                            @error('stock_minimo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="stock_inicial" class="form-label fw-bold small text-secondary">Stock Inicial de Apertura</label>
                            <input type="number" step="0.01" class="form-control @error('stock_inicial') is-invalid @enderror" id="stock_inicial" name="stock_inicial" value="{{ old('stock_inicial', '0.00') }}">
                            @error('stock_inicial') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-4">
                            <label for="precio_referencial" class="form-label fw-bold small text-secondary">Precio Referencial (S/)</label>
                            <input type="number" step="0.01" class="form-control @error('precio_referencial') is-invalid @enderror" id="precio_referencial" name="precio_referencial" value="{{ old('precio_referencial') }}" placeholder="0.00">
                            @error('precio_referencial') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                    </div>

                    <div class="alert alert-info border small mt-3 mb-0">
                        <i class="bi bi-info-circle me-1"></i> Si especifica un <strong>stock inicial mayor a cero</strong>, el sistema generará automáticamente un movimiento de entrada por apertura en el inventario.
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                        <a href="{{ route('productos.index') }}" class="btn btn-light px-4">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">
                            <i class="bi bi-save me-1"></i> Guardar Producto
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
