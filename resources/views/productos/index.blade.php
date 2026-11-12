@extends('layouts.app')

@section('title', 'Catálogo de Productos - ' . config('app.name'))

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold text-dark mb-1"><i class="bi bi-boxes me-2 text-primary"></i>Catálogo de Productos e Insumos</h3>
        <p class="text-secondary small mb-0">Administración de catálogo, unidades de medida y precios referenciales.</p>
    </div>
    @if(in_array(auth()->user()->rol, ['administrador', 'colaborador']))
        <a href="{{ route('productos.create') }}" class="btn btn-primary fw-bold shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Registrar Producto
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
        <form method="GET" action="{{ route('productos.index') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <input type="text" name="buscar" class="form-control" placeholder="Buscar por código o nombre..." value="{{ $buscar }}">
            </div>
            <div class="col-md-3">
                <select name="categoria_id" class="form-select">
                    <option value="">-- Todas las categorías --</option>
                    @foreach ($categorias as $cat)
                        <option value="{{ $cat->id }}" {{ (string)$categoriaId === (string)$cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="estado" class="form-select">
                    <option value="">-- Estado --</option>
                    <option value="1" {{ $estado === '1' ? 'selected' : '' }}>Activos</option>
                    <option value="0" {{ $estado === '0' ? 'selected' : '' }}>Inactivos</option>
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2 align-items-center">
                <div class="form-check me-2">
                    <input type="checkbox" name="stock_bajo" value="1" id="stock_bajo" class="form-check-input" {{ $soloBajo ? 'checked' : '' }}>
                    <label for="stock_bajo" class="form-check-label small fw-bold text-danger">Solo stock bajo</label>
                </div>
                <button type="submit" class="btn btn-secondary px-3">Filtrar</button>
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
                    <th>Producto</th>
                    <th>Categoría</th>
                    <th>Stock Actual</th>
                    <th>Stock Mín.</th>
                    <th>P. Referencial</th>
                    <th class="text-center">Alerta Stock</th>
                    <th class="text-end pe-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($productos as $prod)
                    <tr>
                        <td class="ps-3"><code>{{ $prod->codigo }}</code></td>
                        <td class="fw-bold text-dark">
                            {{ $prod->nombre }}
                            @if(!$prod->activo)
                                <span class="badge bg-secondary ms-1 small">Inactivo</span>
                            @endif
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $prod->categoria->nombre }}</span></td>
                        <td class="fw-bold fs-6">{{ number_format($prod->stock_actual, 2) }} <small class="text-muted fw-normal">{{ $prod->unidad_medida }}</small></td>
                        <td>{{ number_format($prod->stock_minimo, 2) }}</td>
                        <td>{{ $prod->precio_referencial ? 'S/ ' . number_format($prod->precio_referencial, 2) : '-' }}</td>
                        <td class="text-center">
                            @if($prod->stock_actual <= 0)
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><i class="bi bi-x-circle me-1"></i>Agotado</span>
                            @elseif($prod->stock_actual <= $prod->stock_minimo)
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1"><i class="bi bi-exclamation-triangle me-1"></i>Stock Bajo</span>
                            @else
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="bi bi-check-circle me-1"></i>Normal</span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('inventario.kardex', $prod) }}" class="btn btn-outline-info" title="Ver Kardex del producto">
                                    <i class="bi bi-journal-text"></i>
                                </a>
                                @if(in_array(auth()->user()->rol, ['administrador', 'colaborador']))
                                    <a href="{{ route('productos.edit', $prod) }}" class="btn btn-outline-primary" title="Editar producto">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <form method="POST" action="{{ route('productos.toggle-status', $prod) }}" class="d-inline" onsubmit="return confirm('¿Desea cambiar el estado del producto?');">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn {{ $prod->activo ? 'btn-outline-danger' : 'btn-outline-success' }}" title="{{ $prod->activo ? 'Desactivar producto' : 'Activar producto' }}">
                                            <i class="bi {{ $prod->activo ? 'bi-eye-slash' : 'bi-eye' }}"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center py-4 text-muted">No se encontraron productos registrados.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($productos->hasPages())
        <div class="card-footer bg-white border-top py-3">{{ $productos->links() }}</div>
    @endif
</div>
@endsection
