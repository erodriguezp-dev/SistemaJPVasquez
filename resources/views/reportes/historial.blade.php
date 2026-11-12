@extends('layouts.app')

@section('title', 'Historial de Reportes - ' . config('app.name'))

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold text-dark mb-1"><i class="bi bi-clock-history me-2 text-primary"></i>Historial y Auditoría de Reportes</h3>
        <p class="text-secondary small mb-0">Registro de reportes generados y medición de tiempo de respuesta.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('reportes.historial.excel', request()->all()) }}" class="btn btn-success fw-bold"><i class="bi bi-file-earmark-excel me-1"></i> Exportar Historial a Excel</a>
        <a href="{{ route('reportes.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Volver a Reportes</a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('reportes.historial') }}" class="row g-2 align-items-center">
            <div class="col-md-4">
                <select name="tipo" class="form-select">
                    <option value="">-- Todos los tipos --</option>
                    <option value="inventario" {{ $tipo === 'inventario' ? 'selected' : '' }}>Inventario</option>
                    <option value="pedidos" {{ $tipo === 'pedidos' ? 'selected' : '' }}>Pedidos</option>
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" name="fecha_inicio" class="form-control" title="Fecha inicio" value="{{ $fechaInicio }}">
            </div>
            <div class="col-md-3">
                <input type="date" name="fecha_fin" class="form-control" title="Fecha fin" value="{{ $fechaFin }}">
            </div>
            <div class="col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-secondary w-100 fw-bold"><i class="bi bi-filter me-1"></i> Filtrar</button>
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-3 overflow-hidden mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0 small">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Fecha y Hora</th>
                    <th>Tipo Reporte</th>
                    <th class="text-center">Formato</th>
                    <th>Usuario Generador</th>
                    <th class="text-end pe-3">Duración (seg)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($historial as $h)
                    <tr>
                        <td class="ps-3 fw-bold text-dark">{{ $h->hora_inicio?->format('d/m/Y H:i:s') }}</td>
                        <td><span class="badge bg-light text-dark border text-capitalize">{{ $h->tipo }}</span></td>
                        <td class="text-center">
                            @php
                                $fmtBadge = match($h->formato) {
                                    'pdf' => 'bg-danger-subtle text-danger border-danger-subtle',
                                    'excel' => 'bg-success-subtle text-success border-success-subtle',
                                    default => 'bg-primary-subtle text-primary border-primary-subtle',
                                };
                            @endphp
                            <span class="badge {{ $fmtBadge }} border text-uppercase">{{ $h->formato }}</span>
                        </td>
                        <td>{{ $h->user->name }}</td>
                        <td class="text-end pe-3 fw-bold text-primary">{{ number_format($h->duracion_segundos, 3) }} s</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center py-4 text-muted">No hay registros de generación en el historial.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($historial->hasPages())
        <div class="card-footer bg-white border-top py-3">{{ $historial->links() }}</div>
    @endif
</div>

{{-- Bloque de Estadísticas de Rendimiento --}}
<div class="card border-0 shadow-sm rounded-3 bg-light">
    <div class="card-header bg-white py-3 fw-bold"><i class="bi bi-speedometer2 text-primary me-2"></i>Estadísticas de Rendimiento (Registros Filtrados: {{ $stats['total'] }})</div>
    <div class="card-body p-3 text-center">
        <div class="row g-3">
            <div class="col-md-4">
                <div class="p-3 bg-white border rounded shadow-sm">
                    <div class="text-muted small fw-bold mb-1">Duración Promedio</div>
                    <div class="fs-4 fw-bold text-primary">{{ $stats['promedio'] }} seg</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-white border rounded shadow-sm">
                    <div class="text-muted small fw-bold mb-1">Duración Mínima</div>
                    <div class="fs-4 fw-bold text-success">{{ $stats['minimo'] }} seg</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 bg-white border rounded shadow-sm">
                    <div class="text-muted small fw-bold mb-1">Duración Máxima</div>
                    <div class="fs-4 fw-bold text-danger">{{ $stats['maximo'] }} seg</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
