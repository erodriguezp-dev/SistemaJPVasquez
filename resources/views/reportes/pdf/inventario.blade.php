<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte de Inventario - JP Vásquez</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #333; margin: 0; padding: 0; }
        .header { border-bottom: 2px solid #0d6efd; padding-bottom: 8px; margin-bottom: 12px; }
        .company-name { font-size: 16px; font-weight: bold; color: #0d6efd; text-transform: uppercase; }
        .report-title { font-size: 14px; font-weight: bold; margin-top: 4px; }
        .meta-table { width: 100%; margin-bottom: 12px; font-size: 10px; color: #555; }
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        .data-table th, .data-table td { border: 1px solid #dee2e6; padding: 6px; text-align: left; }
        .data-table th { background-color: #f8f9fa; font-weight: bold; }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .badge { padding: 2px 6px; border-radius: 4px; font-size: 9px; font-weight: bold; }
        .bg-success { background-color: #d1e7dd; color: #0f5132; }
        .bg-warning { background-color: #fff3cd; color: #664d03; }
        .bg-danger { background-color: #f8d7da; color: #842029; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; font-size: 9px; text-align: center; color: #777; border-top: 1px solid #ddd; padding-top: 4px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">JP Vasquez Servicios Generales E.I.R.L.</div>
        <div class="report-title">REPORTE DE INVENTARIO Y EXISTENCIAS</div>
    </div>

    <table class="meta-table">
        <tr>
            <td><strong>Fecha de Emisión:</strong> {{ $fecha_emision }}</td>
            <td class="text-end"><strong>Generado Por:</strong> {{ auth()->user()->name }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th>Código</th>
                <th>Producto</th>
                <th>Categoría</th>
                <th class="text-center">Unidad</th>
                <th class="text-center">Stock Actual</th>
                <th class="text-center">Stock Mín.</th>
                <th class="text-center">Estado</th>
            </tr>
        </thead>
        <tbody>
            @forelse($productos as $p)
                <tr>
                    <td><code>{{ $p->codigo }}</code></td>
                    <td><strong>{{ $p->nombre }}</strong></td>
                    <td>{{ $p->categoria->nombre }}</td>
                    <td class="text-center">{{ $p->unidad_medida }}</td>
                    <td class="text-center"><strong>{{ number_format($p->stock_actual, 2) }}</strong></td>
                    <td class="text-center">{{ number_format($p->stock_minimo, 2) }}</td>
                    <td class="text-center">
                        @if($p->stock_actual <= 0)<span class="badge bg-danger">Agotado</span>
                        @elseif($p->stock_actual <= $p->stock_minimo)<span class="badge bg-warning">Stock Bajo</span>
                        @else<span class="badge bg-success">Normal</span>@endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center">No se encontraron registros de inventario.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Sistema JP Vásquez &mdash; Reporte generado en {{ $duracion_segundos ?? 0 }} segundos.
    </div>
</body>
</html>
