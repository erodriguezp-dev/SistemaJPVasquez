@extends('layouts.app')

@section('title', 'Generador de Reportes - ' . config('app.name'))

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold text-dark mb-1"><i class="bi bi-file-earmark-bar-graph me-2 text-primary"></i>Generador de Reportes</h3>
        <p class="text-secondary small mb-0">Seleccione el tipo de reporte, aplique filtros y genere los resultados en Pantalla, PDF o Excel.</p>
    </div>
    <a href="{{ route('reportes.historial') }}" class="btn btn-outline-secondary fw-bold"><i class="bi bi-clock-history me-1"></i> Ver Historial de Generación</a>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show small mb-4"><i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show small mb-4">
        <ul class="mb-0">@foreach ($errors->all() as $err) <li>{{ $err }}</li> @endforeach</ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-4">
        <form method="POST" action="{{ route('reportes.generar') }}" id="formReportes">
            @csrf
            <input type="hidden" name="formato" id="inputFormato" value="pantalla">

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label small fw-bold text-dark">Tipo de Reporte <span class="text-danger">*</span></label>
                    <select name="tipo" id="selectTipo" class="form-select fw-bold border-primary" onchange="toggleFiltros()">
                        <option value="inventario" {{ old('tipo', $data['filtros']['tipo'] ?? '') === 'inventario' ? 'selected' : '' }}>Reporte de Inventario y Existencias</option>
                        <option value="pedidos" {{ old('tipo', $data['filtros']['tipo'] ?? '') === 'pedidos' ? 'selected' : '' }}>Reporte de Pedidos de Clientes</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Fecha Inicio</label>
                    <input type="date" name="fecha_inicio" class="form-control" value="{{ old('fecha_inicio', $data['filtros']['fecha_inicio'] ?? '') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Fecha Fin</label>
                    <input type="date" name="fecha_fin" class="form-control" value="{{ old('fecha_fin', $data['filtros']['fecha_fin'] ?? '') }}">
                </div>
            </div>

            <!-- Filtros para Inventario -->
            <div id="filtrosInventario" class="row g-3 mb-3 p-3 bg-light rounded">
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Categoría</label>
                    <select name="categoria_id" class="form-select form-select-sm">
                        <option value="">-- Todas las categorías --</option>
                        @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}" {{ (string)old('categoria_id', $data['filtros']['categoria_id'] ?? '') === (string)$cat->id ? 'selected' : '' }}>{{ $cat->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Producto Específico</label>
                    <select name="producto_id" class="form-select form-select-sm">
                        <option value="">-- Todos los productos --</option>
                        @foreach($productos as $prod)
                            <option value="{{ $prod->id }}" {{ (string)old('producto_id', $data['filtros']['producto_id'] ?? '') === (string)$prod->id ? 'selected' : '' }}>{{ $prod->codigo }} - {{ $prod->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Estado de Stock</label>
                    <select name="estado_stock" class="form-select form-select-sm">
                        <option value="">-- Todos los estados --</option>
                        <option value="normal" {{ old('estado_stock', $data['filtros']['estado_stock'] ?? '') === 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="bajo" {{ old('estado_stock', $data['filtros']['estado_stock'] ?? '') === 'bajo' ? 'selected' : '' }}>Stock Bajo</option>
                        <option value="agotado" {{ old('estado_stock', $data['filtros']['estado_stock'] ?? '') === 'agotado' ? 'selected' : '' }}>Agotado</option>
                    </select>
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input type="checkbox" name="incluir_movimientos" value="1" id="chkMovs" class="form-check-input" {{ !empty($data['incluir_movimientos']) ? 'checked' : '' }}>
                        <label for="chkMovs" class="form-check-label small fw-bold text-dark">Incluir historial de movimientos del periodo seleccionado</label>
                    </div>
                </div>
            </div>

            <!-- Filtros para Pedidos -->
            <div id="filtrosPedidos" class="row g-3 mb-3 p-3 bg-light rounded" style="display: none;">
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Tipo de Pedido</label>
                    <select name="tipo_pedido" class="form-select form-select-sm">
                        <option value="">-- Todos los tipos --</option>
                        <option value="mueble" {{ old('tipo_pedido', $data['filtros']['tipo_pedido'] ?? '') === 'mueble' ? 'selected' : '' }}>Mueble</option>
                        <option value="ferreteria" {{ old('tipo_pedido', $data['filtros']['tipo_pedido'] ?? '') === 'ferreteria' ? 'selected' : '' }}>Ferretería</option>
                        <option value="reparacion" {{ old('tipo_pedido', $data['filtros']['tipo_pedido'] ?? '') === 'reparacion' ? 'selected' : '' }}>Reparación</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-bold">Estado del Pedido</label>
                    <select name="estado_pedido" class="form-select form-select-sm">
                        <option value="">-- Todos los estados --</option>
                        <option value="recibido" {{ old('estado_pedido', $data['filtros']['estado_pedido'] ?? '') === 'recibido' ? 'selected' : '' }}>Recibido</option>
                        <option value="en_proceso" {{ old('estado_pedido', $data['filtros']['estado_pedido'] ?? '') === 'en_proceso' ? 'selected' : '' }}>En Proceso</option>
                        <option value="terminado" {{ old('estado_pedido', $data['filtros']['estado_pedido'] ?? '') === 'terminado' ? 'selected' : '' }}>Terminado</option>
                        <option value="entregado" {{ old('estado_pedido', $data['filtros']['estado_pedido'] ?? '') === 'entregado' ? 'selected' : '' }}>Entregado</option>
                        <option value="confirmado" {{ old('estado_pedido', $data['filtros']['estado_pedido'] ?? '') === 'confirmado' ? 'selected' : '' }}>Confirmado</option>
                    </select>
                </div>
            </div>

            <!-- Botones de Acción Formato -->
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-primary fw-bold" onclick="generarFormato('pantalla')"><i class="bi bi-display me-1"></i> Ver en Pantalla</button>
                <button type="button" class="btn btn-danger fw-bold" onclick="generarFormato('pdf')"><i class="bi bi-file-earmark-pdf me-1"></i> Descargar PDF</button>
                <button type="button" class="btn btn-success fw-bold" onclick="generarFormato('excel')"><i class="bi bi-file-earmark-excel me-1"></i> Descargar Excel</button>
            </div>
        </form>
    </div>
</div>

{{-- Resultados en Pantalla --}}
@if(isset($data))
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
            <span class="fw-bold"><i class="bi bi-table text-primary me-2"></i>Resultados del Reporte de {{ ucfirst($data['filtros']['tipo']) }}</span>
            <span class="badge bg-secondary-subtle text-dark border">Emisión: {{ $data['fecha_emision'] }}</span>
        </div>
        <div class="table-responsive">
            @if($data['filtros']['tipo'] === 'inventario')
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light"><tr><th class="ps-3">Código</th><th>Producto</th><th>Categoría</th><th class="text-center">Stock Actual</th><th class="text-center">Stock Mín.</th><th class="text-center">Estado</th></tr></thead>
                    <tbody>
                        @forelse($data['productos'] as $p)
                            <tr>
                                <td class="ps-3"><code>{{ $p->codigo }}</code></td>
                                <td class="fw-bold text-dark">{{ $p->nombre }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $p->categoria->nombre }}</span></td>
                                <td class="text-center fw-bold">{{ number_format($p->stock_actual, 2) }} {{ $p->unidad_medida }}</td>
                                <td class="text-center">{{ number_format($p->stock_minimo, 2) }}</td>
                                <td class="text-center">
                                    @if($p->stock_actual <= 0)<span class="badge bg-danger-subtle text-danger border">Agotado</span>
                                    @elseif($p->stock_actual <= $p->stock_minimo)<span class="badge bg-warning-subtle text-warning-emphasis border">Stock Bajo</span>
                                    @else<span class="badge bg-success-subtle text-success border">Normal</span>@endif
                                </td>
                            </tr>
                        @empty <tr><td colspan="6" class="text-center py-4 text-muted">No se encontraron productos con los filtros seleccionados.</td></tr> @endforelse
                    </tbody>
                </table>
            @else
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="table-light"><tr><th class="ps-3">Código</th><th>Fecha Pedido</th><th>Tipo</th><th>Cliente</th><th class="text-center">Estado</th><th class="text-center">Fecha Entrega</th><th class="text-center">Ítems</th></tr></thead>
                    <tbody>
                        @forelse($data['pedidos'] as $ped)
                            <tr>
                                <td class="ps-3"><code>{{ $ped->codigo }}</code></td>
                                <td>{{ $ped->fecha_pedido?->format('d/m/Y H:i') }}</td>
                                <td><span class="badge bg-light text-dark border text-capitalize">{{ $ped->tipo }}</span></td>
                                <td class="fw-bold text-dark">{{ $ped->cliente_nombre }}</td>
                                <td class="text-center"><span class="badge bg-primary-subtle text-primary border text-capitalize">{{ str_replace('_', ' ', $ped->estado) }}</span></td>
                                <td class="text-center">{{ $ped->fecha_entrega?->format('d/m/Y') ?? '-' }}</td>
                                <td class="text-center fw-bold">{{ $ped->detalles->count() }}</td>
                            </tr>
                        @empty <tr><td colspan="7" class="text-center py-4 text-muted">No se encontraron pedidos con los filtros seleccionados.</td></tr> @endforelse
                    </tbody>
                </table>
            @endif
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
    function toggleFiltros() {
        const tipo = document.getElementById('selectTipo').value;
        document.getElementById('filtrosInventario').style.display = (tipo === 'inventario') ? 'flex' : 'none';
        document.getElementById('filtrosPedidos').style.display = (tipo === 'pedidos') ? 'flex' : 'none';
    }
    function generarFormato(fmt) {
        document.getElementById('inputFormato').value = fmt;
        const form = document.getElementById('formReportes');
        if (fmt !== 'pantalla') { form.target = '_blank'; } else { form.target = '_self'; }
        form.submit();
    }
    document.addEventListener('DOMContentLoaded', toggleFiltros);
</script>
@endpush
