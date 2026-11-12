@extends('layouts.app')

@section('title', 'Registrar Pedido - ' . config('app.name'))

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h3 class="fw-bold text-dark mb-1"><i class="bi bi-cart-plus me-2 text-primary"></i>Registrar Nuevo Pedido</h3>
        <p class="text-secondary small mb-0">Complete los datos del cliente y agregue las líneas de detalle.</p>
    </div>
    <a href="{{ route('pedidos.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left me-1"></i> Volver al listado</a>
</div>

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show small mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<form method="POST" action="{{ route('pedidos.store') }}" id="pedidoForm">
    @csrf
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 fw-bold"><i class="bi bi-person-lines-fill me-2 text-primary"></i>Datos del Cliente y Pedido</div>
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Tipo de Pedido <span class="text-danger">*</span></label>
                    <select name="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
                        <option value="mueble" {{ old('tipo') === 'mueble' ? 'selected' : '' }}>Mueble</option>
                        <option value="ferreteria" {{ old('tipo') === 'ferreteria' ? 'selected' : '' }}>Ferretería</option>
                        <option value="reparacion" {{ old('tipo') === 'reparacion' ? 'selected' : '' }}>Reparación</option>
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label small fw-bold">Nombre del Cliente <span class="text-danger">*</span></label>
                    <input type="text" name="cliente_nombre" class="form-control @error('cliente_nombre') is-invalid @enderror" placeholder="Nombre o Razón Social" value="{{ old('cliente_nombre') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Teléfono de Contacto (9 dígitos)</label>
                    <input type="text" name="cliente_telefono" class="form-control @error('cliente_telefono') is-invalid @enderror" placeholder="Ej. 987654321" value="{{ old('cliente_telefono') }}" maxlength="9" pattern="[0-9]{9}" oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,9)">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Dirección de Entrega</label>
                    <input type="text" name="cliente_direccion" class="form-control" placeholder="Dirección del cliente" value="{{ old('cliente_direccion') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-bold">Fecha de Entrega Estimada</label>
                    <input type="date" name="fecha_entrega" class="form-control" value="{{ old('fecha_entrega') }}">
                </div>
                <div class="col-12">
                    <label class="form-label small fw-bold">Observaciones Generales</label>
                    <textarea name="observaciones" class="form-control" rows="2" placeholder="Notas adicionales sobre la entrega o el pedido...">{{ old('observaciones') }}</textarea>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
            <span class="fw-bold"><i class="bi bi-list-task me-2 text-primary"></i>Líneas de Detalle del Pedido</span>
            <button type="button" class="btn btn-sm btn-outline-primary fw-bold" id="btnAgregarLinea"><i class="bi bi-plus-circle me-1"></i> Agregar Línea</button>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0" id="tablaDetalles">
                <thead class="table-light">
                    <tr>
                        <th style="width: 25%;">Producto (Inventario)</th>
                        <th style="width: 35%;">Descripción del Ítem <span class="text-danger">*</span></th>
                        <th style="width: 15%;">Cantidad <span class="text-danger">*</span></th>
                        <th style="width: 15%;">P. Unit. (S/)</th>
                        <th style="width: 10%;" class="text-end pe-3">Acción</th>
                    </tr>
                </thead>
                <tbody id="detallesBody"></tbody>
            </table>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2 mb-4">
        <a href="{{ route('pedidos.index') }}" class="btn btn-secondary px-4">Cancelar</a>
        <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm"><i class="bi bi-save me-1"></i> Guardar Pedido</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
    const productosData = @json($productos);
    let rowIndex = 0;

    function agregarLinea(prodId = '', desc = '', cant = 1, precio = '') {
        const tbody = document.getElementById('detallesBody');
        const tr = document.createElement('tr');
        tr.id = `row-${rowIndex}`;

        let options = '<option value="">-- Ninguno (Solo servicio / descripción) --</option>';
        productosData.forEach(p => {
            const sel = (String(p.id) === String(prodId)) ? 'selected' : '';
            options += `<option value="${p.id}" ${sel} data-stock="${p.stock_actual}" data-nombre="${p.nombre}" data-precio="${p.precio_referencial || ''}">${p.codigo} - ${p.nombre} (Stock: ${p.stock_actual})</option>`;
        });

        tr.innerHTML = `
            <td>
                <select name="detalles[${rowIndex}][producto_id]" class="form-select form-select-sm prod-select" onchange="onProductoChange(this, ${rowIndex})">${options}</select>
                <div class="stock-info mt-1 small text-muted"></div>
            </td>
            <td>
                <input type="text" name="detalles[${rowIndex}][descripcion]" class="form-control form-control-sm desc-input" value="${desc}" placeholder="Descripción del trabajo/producto" required>
            </td>
            <td>
                <input type="number" step="0.01" min="0.01" name="detalles[${rowIndex}][cantidad]" class="form-control form-control-sm" value="${cant}" required>
            </td>
            <td>
                <input type="number" step="0.01" min="0" name="detalles[${rowIndex}][precio_unitario]" class="form-control form-control-sm precio-input" value="${precio}" placeholder="0.00">
            </td>
            <td class="text-end pe-3">
                <button type="button" class="btn btn-outline-danger btn-sm" onclick="eliminarLinea(${rowIndex})" title="Quitar línea"><i class="bi bi-trash"></i></button>
            </td>
        `;
        tbody.appendChild(tr);
        if (prodId) onProductoChange(tr.querySelector('.prod-select'), rowIndex);
        rowIndex++;
    }

    function onProductoChange(select, idx) {
        const row = document.getElementById(`row-${idx}`);
        const opt = select.options[select.selectedIndex];
        const descInput = row.querySelector('.desc-input');
        const precioInput = row.querySelector('.precio-input');
        const stockInfo = row.querySelector('.stock-info');

        if (select.value) {
            const stock = parseFloat(opt.getAttribute('data-stock') || 0);
            const nombre = opt.getAttribute('data-nombre');
            const precio = opt.getAttribute('data-precio');

            stockInfo.innerHTML = `<span class="badge ${stock > 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'} border">Stock disp: ${stock}</span>`;
            if (!descInput.value) descInput.value = nombre;
            if (!precioInput.value && precio) precioInput.value = precio;
        } else {
            stockInfo.innerHTML = '';
        }
    }

    function eliminarLinea(idx) {
        const tbody = document.getElementById('detallesBody');
        if (tbody.children.length <= 1) {
            alert('El pedido debe tener al menos una línea de detalle.');
            return;
        }
        document.getElementById(`row-${idx}`).remove();
    }

    document.getElementById('btnAgregarLinea').addEventListener('click', () => agregarLinea());
    document.addEventListener('DOMContentLoaded', () => agregarLinea());
</script>
@endpush
