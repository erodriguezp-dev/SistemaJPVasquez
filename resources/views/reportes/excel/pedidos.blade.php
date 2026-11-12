<table>
    <thead>
        <tr>
            <th colspan="7" style="font-weight: bold; font-size: 14px;">JP Vasquez Servicios Generales E.I.R.L. - REPORTE DE PEDIDOS DE CLIENTES</th>
        </tr>
        <tr>
            <th colspan="7" style="color: #555;">Fecha de Emisión: {{ $fecha_emision }} | Generado Por: {{ auth()->user()->name }}</th>
        </tr>
        <tr><th></th></tr>
        <tr>
            <th style="font-weight: bold; background-color: #e9ecef;">Código</th>
            <th style="font-weight: bold; background-color: #e9ecef;">Fecha Pedido</th>
            <th style="font-weight: bold; background-color: #e9ecef;">Tipo</th>
            <th style="font-weight: bold; background-color: #e9ecef;">Cliente</th>
            <th style="font-weight: bold; background-color: #e9ecef;">Estado</th>
            <th style="font-weight: bold; background-color: #e9ecef;">Fecha Entrega</th>
            <th style="font-weight: bold; background-color: #e9ecef;">Cant. Ítems</th>
        </tr>
    </thead>
    <tbody>
        @foreach($pedidos as $ped)
            <tr>
                <td>{{ $ped->codigo }}</td>
                <td>{{ $ped->fecha_pedido?->format('d/m/Y H:i') }}</td>
                <td>{{ ucfirst($ped->tipo) }}</td>
                <td>{{ $ped->cliente_nombre }}</td>
                <td>{{ ucfirst(str_replace('_', ' ', $ped->estado)) }}</td>
                <td>{{ $ped->fecha_entrega?->format('d/m/Y') ?? '-' }}</td>
                <td>{{ $ped->detalles->count() }}</td>
            </tr>
        @endforeach
        <tr><th></th></tr>
        <tr>
            <td colspan="7" style="font-weight: bold; background-color: #f8f9fa;">RESUMEN DE TOTALES:</td>
        </tr>
        <tr>
            <td colspan="7">Por Estado: Recibidos: {{ $totales_estado['recibido'] }} | En Proceso: {{ $totales_estado['en_proceso'] }} | Terminados: {{ $totales_estado['terminado'] }} | Entregados: {{ $totales_estado['entregado'] }} | Confirmados: {{ $totales_estado['confirmado'] }}</td>
        </tr>
        <tr>
            <td colspan="7">Por Tipo: Mueble: {{ $totales_tipo['mueble'] }} | Ferretería: {{ $totales_tipo['ferreteria'] }} | Reparación: {{ $totales_tipo['reparacion'] }}</td>
        </tr>
    </tbody>
</table>
