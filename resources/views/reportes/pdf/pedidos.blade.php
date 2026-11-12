<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Reporte de Pedidos - JP Vásquez</title>
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
        .footer { position: fixed; bottom: 0; left: 0; right: 0; font-size: 9px; text-align: center; color: #777; border-top: 1px solid #ddd; padding-top: 4px; }
        .summary-box { background-color: #f8f9fa; border: 1px solid #ddd; padding: 8px; margin-top: 12px; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="company-name">JP Vasquez Servicios Generales E.I.R.L.</div>
        <div class="report-title">REPORTE DE PEDIDOS DE CLIENTES</div>
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
                <th>Fecha Pedido</th>
                <th>Tipo</th>
                <th>Cliente</th>
                <th class="text-center">Estado</th>
                <th class="text-center">Fecha Entrega</th>
                <th class="text-center">Ítems</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pedidos as $ped)
                <tr>
                    <td><code>{{ $ped->codigo }}</code></td>
                    <td>{{ $ped->fecha_pedido?->format('d/m/Y H:i') }}</td>
                    <td class="text-capitalize">{{ $ped->tipo }}</td>
                    <td><strong>{{ $ped->cliente_nombre }}</strong></td>
                    <td class="text-center text-capitalize">{{ str_replace('_', ' ', $ped->estado) }}</td>
                    <td class="text-center">{{ $ped->fecha_entrega?->format('d/m/Y') ?? '-' }}</td>
                    <td class="text-center">{{ $ped->detalles->count() }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center">No se encontraron registros de pedidos.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="summary-box">
        <strong>RESUMEN DE TOTALES:</strong>
        <table style="width: 100%; font-size: 10px; margin-top: 6px;">
            <tr>
                <td><strong>Por Estado:</strong> Recibidos: {{ $totales_estado['recibido'] }} | En Proceso: {{ $totales_estado['en_proceso'] }} | Terminados: {{ $totales_estado['terminado'] }} | Entregados: {{ $totales_estado['entregado'] }} | Confirmados: {{ $totales_estado['confirmado'] }}</td>
            </tr>
            <tr>
                <td><strong>Por Tipo:</strong> Mueble: {{ $totales_tipo['mueble'] }} | Ferretería: {{ $totales_tipo['ferreteria'] }} | Reparación: {{ $totales_tipo['reparacion'] }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        Sistema JP Vásquez &mdash; Reporte generado en {{ $duracion_segundos ?? 0 }} segundos.
    </div>
</body>
</html>
