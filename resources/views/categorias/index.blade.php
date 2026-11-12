@extends('layouts.app')

@section('title', 'Categorías - ' . config('app.name'))

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold text-dark mb-1"><i class="bi bi-tags me-2 text-primary"></i>Categorías de Productos</h3>
        <p class="text-secondary small mb-0">Gestión de categorías para clasificación de insumos y productos.</p>
    </div>
    @if(in_array(auth()->user()->rol, ['administrador']))
        <button type="button" class="btn btn-primary fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#modalCrearCategoria">
            <i class="bi bi-plus-lg me-1"></i> Nueva Categoría
        </button>
    @endif
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show small mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show small mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-1"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card border-0 shadow-sm rounded-3 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Nombre</th>
                    <th>Descripción</th>
                    <th class="text-center">Productos Vinculados</th>
                    @if(in_array(auth()->user()->rol, ['administrador']))
                        <th class="text-end pe-3">Acciones</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse ($categorias as $cat)
                    <tr>
                        <td class="ps-3 fw-bold text-dark">{{ $cat->nombre }}</td>
                        <td class="text-secondary small">{{ $cat->descripcion ?? 'Sin descripción' }}</td>
                        <td class="text-center">
                            <span class="badge bg-secondary-subtle text-secondary border px-3 py-1 rounded-pill">
                                {{ $cat->productos_count }} productos
                            </span>
                        </td>
                        @if(in_array(auth()->user()->rol, ['administrador']))
                            <td class="text-end pe-3">
                                <div class="btn-group btn-group-sm">
                                    <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalEditarCategoria{{ $cat->id }}" title="Editar categoría">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <form method="POST" action="{{ route('categorias.destroy', $cat) }}" class="d-inline" onsubmit="return confirm('¿Está seguro de eliminar esta categoría?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Eliminar categoría" {{ $cat->productos_count > 0 ? 'disabled' : '' }}>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>

                                <!-- Modal Editar -->
                                <div class="modal fade text-start" id="modalEditarCategoria{{ $cat->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST" action="{{ route('categorias.update', $cat) }}">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-header">
                                                    <h5 class="modal-title fw-bold">Editar Categoría</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold small">Nombre *</label>
                                                        <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $cat->nombre) }}" required max="80">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold small">Descripción</label>
                                                        <textarea name="descripcion" class="form-control" rows="2" max="255">{{ old('descripcion', $cat->descripcion) }}</textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                                                    <button type="submit" class="btn btn-primary fw-bold">Actualizar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        @endif
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center py-4 text-muted">No se encontraron categorías registradas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($categorias->hasPages())
        <div class="card-footer bg-white border-top py-3">{{ $categorias->links() }}</div>
    @endif
</div>

<!-- Modal Crear Categoría -->
@if(in_array(auth()->user()->rol, ['administrador']))
    <div class="modal fade" id="modalCrearCategoria" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" action="{{ route('categorias.store') }}">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Nueva Categoría</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Nombre de la Categoría *</label>
                            <input type="text" name="nombre" class="form-control" placeholder="Ej. Carpintería y Madera" required max="80">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">Descripción (Opcional)</label>
                            <textarea name="descripcion" class="form-control" rows="2" placeholder="Breve descripción..." max="255"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary fw-bold">Guardar Categoría</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection
