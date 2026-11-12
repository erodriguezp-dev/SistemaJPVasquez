@extends('layouts.app')
@section('title', 'Dashboard General - ' . config('app.name'))
@section('content')
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
        <h3 class="fw-bold text-dark mb-1"><i class="bi bi-speedometer2 me-2 text-primary"></i>Dashboard General</h3>
        <p class="text-secondary small mb-0">Vista ejecutiva e indicadores clave de rendimiento del negocio.</p>
    </div>
    <form method="GET" action="{{ route('reportes.dashboard') }}" class="d-flex gap-2 align-items-center flex-wrap">
        <select name="periodo" id="selectPeriodo" class="form-select form-select-sm fw-bold border-primary" onchange="toggleFechas()">
            <option value="hoy" {{ $rango['periodo'] === 'hoy' ? 'selected' : '' }}>Hoy</option>
            <option value="7_dias" {{ $rango['periodo'] === '7_dias' ? 'selected' : '' }}>Últimos 7 días</option>
            <option value="30_dias" {{ $rango['periodo'] === '30_dias' ? 'selected' : '' }}>Últimos 30 días</option>
            <option value="este_mes" {{ $rango['periodo'] === 'este_mes' ? 'selected' : '' }}>Este mes</option>
            <option value="personalizado" {{ $rango['periodo'] === 'personalizado' ? 'selected' : '' }}>Rango personalizado</option>
        </select>
        <div id="divFechas" class="d-flex gap-1" style="{{ $rango['periodo'] === 'personalizado' ? '' : 'display:none;' }}">
            <input type="date" name="fecha_inicio" class="form-control form-control-sm" value="{{ request('fecha_inicio', $rango['start']->format('Y-m-d')) }}">
            <input type="date" name="fecha_fin" class="form-control form-control-sm" value="{{ request('fecha_fin', $rango['end']->format('Y-m-d')) }}">
        </div>
        <button type="submit" class="btn btn-sm btn-primary fw-bold"><i class="bi bi-filter me-1"></i>Aplicar</button>
    </form>
</div>

<div class="row row-cols-1 row-cols-md-2 row-cols-xl-4 g-3 mb-4">
    <div class="col"><div class="card border-0 shadow-sm border-start border-primary border-4 p-3 bg-white">
        <div class="d-flex justify-content-between align-items-center"><div class="text-muted small fw-bold">Pedidos Registrados</div><i class="bi bi-cart-check fs-3 text-primary"></i></div>
        <div class="fs-2 fw-bold text-dark mt-1">{{ number_format($kpis['pedidos_registrados']) }}</div>
    </div></div>
    <div class="col"><div class="card border-0 shadow-sm border-start border-success border-4 p-3 bg-white">
        <div class="d-flex justify-content-between align-items-center"><div class="text-muted small fw-bold">Pedidos Entregados/Confirmados</div><i class="bi bi-check2-circle fs-3 text-success"></i></div>
        <div class="fs-2 fw-bold text-dark mt-1">{{ number_format($kpis['pedidos_completados']) }}</div>
    </div></div>
    <div class="col"><div class="card border-0 shadow-sm border-start border-info border-4 p-3 bg-white">
        <div class="d-flex justify-content-between align-items-center"><div class="text-muted small fw-bold">Unidades Despachadas</div><i class="bi bi-box-arrow-up-right fs-3 text-info"></i></div>
        <div class="fs-2 fw-bold text-dark mt-1">{{ number_format($kpis['unidades_despachadas'], 2) }}</div>
    </div></div>
    <div class="col"><div class="card border-0 shadow-sm border-start border-warning border-4 p-3 bg-white">
        <div class="d-flex justify-content-between align-items-center"><div class="text-muted small fw-bold">Productos Alerta Stock</div><i class="bi bi-exclamation-triangle fs-3 text-warning"></i></div>
        <div class="fs-2 fw-bold text-dark mt-1">{{ number_format($kpis['productos_alerta']) }}</div>
    </div></div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-8"><div class="card border-0 shadow-sm h-100"><div class="card-header bg-white fw-bold py-3"><i class="bi bi-bar-chart-line text-primary me-2"></i>Top 10 Productos Más Vendidos</div>
        <div class="card-body">@if(empty($chartTopProductos['labels']))<div class="p-5 text-center text-muted"><i class="bi bi-bar-chart fs-1 opacity-50"></i><p class="mt-2 mb-0">Sin datos para el periodo seleccionado</p></div>@else<canvas id="chartTop" height="130"></canvas>@endif</div>
    </div></div>
    <div class="col-lg-4"><div class="card border-0 shadow-sm h-100"><div class="card-header bg-white fw-bold py-3"><i class="bi bi-pie-chart text-primary me-2"></i>Pedidos por Estado</div>
        <div class="card-body">@if(empty($chartEstados['labels']))<div class="p-5 text-center text-muted"><i class="bi bi-pie-chart fs-1 opacity-50"></i><p class="mt-2 mb-0">Sin datos para el periodo seleccionado</p></div>@else<canvas id="chartEstados" height="220"></canvas>@endif</div>
    </div></div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-4"><div class="card border-0 shadow-sm h-100"><div class="card-header bg-white fw-bold py-3"><i class="bi bi-diagram-3 text-primary me-2"></i>Pedidos por Tipo</div>
        <div class="card-body">@if(empty($chartTipos['values']) || array_sum($chartTipos['values']) == 0)<div class="p-5 text-center text-muted"><i class="bi bi-bar-chart-steps fs-1 opacity-50"></i><p class="mt-2 mb-0">Sin datos para el periodo seleccionado</p></div>@else<canvas id="chartTipos" height="220"></canvas>@endif</div>
    </div></div>
    <div class="col-lg-8"><div class="card border-0 shadow-sm h-100"><div class="card-header bg-white fw-bold py-3"><i class="bi bi-graph-up text-primary me-2"></i>Evolución de Pedidos en el Tiempo</div>
        <div class="card-body">@if(empty($chartEvolucion['labels']))<div class="p-5 text-center text-muted"><i class="bi bi-graph-up-arrow fs-1 opacity-50"></i><p class="mt-2 mb-0">Sin datos para el periodo seleccionado</p></div>@else<canvas id="chartEvolucion" height="120"></canvas>@endif</div>
    </div></div>
</div>

<div class="card border-0 shadow-sm mb-4"><div class="card-header bg-white fw-bold py-3"><i class="bi bi-arrow-down-up text-primary me-2"></i>Movimientos de Inventario (Entradas vs Salidas)</div>
    <div class="card-body">@if(empty($chartMovimientos['labels']))<div class="p-5 text-center text-muted"><i class="bi bi-calendar-week fs-1 opacity-50"></i><p class="mt-2 mb-0">Sin datos para el periodo seleccionado</p></div>@else<canvas id="chartMovs" height="90"></canvas>@endif</div>
</div>
@endsection

@push('scripts')
<script>
    function toggleFechas(){ document.getElementById('divFechas').style.display = document.getElementById('selectPeriodo').value === 'personalizado' ? 'flex' : 'none'; }
    document.addEventListener('DOMContentLoaded', function(){
        if(window.Chart){
            @if(!empty($chartTopProductos['labels']))
            new Chart(document.getElementById('chartTop'), { type: 'bar', data: { labels: @json($chartTopProductos['labels']), datasets: [{ label: 'Unidades Vendidas', data: @json($chartTopProductos['values']), backgroundColor: '#0d6efd' }] }, options: { indexAxis: 'y', responsive: true, plugins: { legend: { display: false } } } });
            @endif
            @if(!empty($chartEstados['labels']))
            new Chart(document.getElementById('chartEstados'), { type: 'doughnut', data: { labels: @json($chartEstados['labels']), datasets: [{ data: @json($chartEstados['values']), backgroundColor: @json($chartEstados['colors']) }] }, options: { responsive: true } });
            @endif
            @if(!empty($chartTipos['values']) && array_sum($chartTipos['values']) > 0)
            new Chart(document.getElementById('chartTipos'), { type: 'bar', data: { labels: @json($chartTipos['labels']), datasets: [{ label: 'Pedidos', data: @json($chartTipos['values']), backgroundColor: ['#0d6efd', '#198754', '#ffc107'] }] }, options: { responsive: true, plugins: { legend: { display: false } } } });
            @endif
            @if(!empty($chartEvolucion['labels']))
            new Chart(document.getElementById('chartEvolucion'), { type: 'line', data: { labels: @json($chartEvolucion['labels']), datasets: [{ label: 'Total Pedidos', data: @json($chartEvolucion['values']), borderColor: '#0d6efd', backgroundColor: 'rgba(13, 110, 253, 0.1)', fill: true, tension: 0.3 }] }, options: { responsive: true, plugins: { legend: { display: false } } } });
            @endif
            @if(!empty($chartMovimientos['labels']))
            new Chart(document.getElementById('chartMovs'), { type: 'bar', data: { labels: @json($chartMovimientos['labels']), datasets: [ { label: 'Entradas', data: @json($chartMovimientos['entradas']), backgroundColor: '#198754' }, { label: 'Salidas', data: @json($chartMovimientos['salidas']), backgroundColor: '#dc3545' } ] }, options: { responsive: true } });
            @endif
        }
    });
</script>
@endpush
