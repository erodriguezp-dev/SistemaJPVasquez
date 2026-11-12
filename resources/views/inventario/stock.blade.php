@extends('layouts.app')

@section('title', 'Stock en Tiempo Real - ' . config('app.name'))

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold text-dark mb-1"><i class="bi bi-speedometer me-2 text-primary"></i>Stock en Tiempo Real</h3>
        <p class="text-secondary small mb-0">Monitoreo de existencias, niveles de stock mínimo y estado de alerta de insumos.</p>
    </div>
    @if(in_array(auth()->user()->rol, ['administrador', 'colaborador']))
        <div class="d-flex gap-2">
            <a href="{{ route('inventario.ajuste.create') }}" class="btn btn-warning fw-bold shadow-sm">
                <i class="bi bi-sliders me-1"></i> Ajustar Stock Físico
            </a>
            <a href="{{ route('movimientos.create') }}" class="btn btn-primary fw-bold shadow-sm">
                <i class="bi bi-arrow-down-up me-1"></i> Nuevo Movimiento
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

<!-- Tarjetas de Resumen -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body d-flex align-items-center p-3">
                <div class="bg-primary-subtle text-primary p-3 rounded-3 me-3"><i class="bi bi-boxes fs-3"></i></div>
                <div>
                    <div class="text-muted small fw-semibold">Total Catálogo</div>
                    <div class="fs-4 fw-bold text-dark">{{ $totalProductos }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body d-flex align-items-center p-3">
                <div class="bg-warning-subtle text-warning-emphasis p-3 rounded-3 me-3"><i class="bi bi-exclamation-triangle fs-3"></i></div>
                <div>
                    <div class="text-muted small fw-semibold">Stock Bajo</div>
                    <div class="fs-4 fw-bold text-warning-emphasis">{{ $totalStockBajo }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body d-flex align-items-center p-3">
                <div class="bg-danger-subtle text-danger p-3 rounded-3 me-3"><i class="bi bi-x-circle fs-3"></i></div>
                <div>
                    <div class="text-muted small fw-semibold">Agotados (Cero)</div>
                    <div class="fs-4 fw-bold text-danger">{{ $totalAgotados }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm rounded-3 h-100">
            <div class="card-body d-flex align-items-center p-3">
                <div class="bg-info-subtle text-info p-3 rounded-3 me-3"><i class="bi bi-bell fs-3"></i></div>
                <div>
                    <div class="text-muted small fw-semibold">Total en Alerta</div>
                    <div class="fs-4 fw-bold text-dark">{{ $totalAlertas }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('inventario.stock') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
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
            <div class="col-md-3">
                <select name="estado_stock" class="form-select">
                    <option value="">-- Estado de Stock --</option>
                    <option value="normal" {{ $estadoStock === 'normal' ? 'selected' : '' }}>Stock Normal</option>
                    <option value="bajo" {{ $estadoStock === 'bajo' ? 'selected' : '' }}>Stock Bajo</option>
                    <option value="agotado" {{ $estadoStock === 'agotado' ? 'selected' : '' }}>Agotados</option>
                    <option value="alerta" {{ $estadoStock === 'alerta' ? 'selected' : '' }}>Alertas (Bajo + Agotado)</option>
                </select>
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-secondary w-100 fw-bold">Filtrar</button>
            </div>
        </form>
    </div>
</div>

<!-- Tabla -->
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
                    <th class="text-center">Estado del Stock</th>
                    <th class="text-end pe-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($productos as $prod)
                    <tr>
                        <td class="ps-3"><code>{{ $prod->codigo }}</code></td>
                        <td class="fw-bold text-dark">{{ $prod->nombre }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $prod->categoria->nombre }}</span></td>
                        <td class="fw-bold fs-6">{{ number_format($prod->stock_actual, 2) }} <small class="text-muted fw-normal">{{ $prod->unidad_medida }}</small></td>
                        <td>{{ number_format($prod->stock_minimo, 2) }}</td>
                        <td class="text-center">
                            @if($prod->stock_actual <= 0)
                                <span class="badge bg-danger text-white px-3 py-1 rounded-pill"><i class="bi bi-x-circle me-1"></i>Agotado</span>
                            @elseif($prod->stock_actual <= $prod->stock_minimo)
                                <span class="badge bg-warning text-dark px-3 py-1 rounded-pill"><i class="bi bi-exclamation-triangle me-1"></i>Stock Bajo</span>
                            @else
                                <span class="badge bg-success text-white px-3 py-1 rounded-pill"><i class="bi bi-check-circle me-1"></i>Normal</span>
                            @endif
                        </td>
                        <td class="text-end pe-3">
                            <div class="btn-group btn-group-sm">
                                <a href="{{ route('inventario.kardex', $prod) }}" class="btn btn-outline-info" title="Ver Kardex">
                                    <i class="bi bi-journal-text me-1"></i> Kardex
                                </a>
                                @if(in_array(auth()->user()->rol, ['administrador', 'colaborador']))
                                    <a href="{{ route('inventario.ajuste.create', ['producto_id' => $prod->id]) }}" class="btn btn-outline-warning" title="Ajustar stock">
                                        <i class="bi bi-sliders"></i>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">No se encontraron productos en la consulta de stock.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($productos->hasPages())
        <div class="card-footer bg-white border-top py-3">{{ $productos->links() }}</div>
    @endif
</div>
@endsection
