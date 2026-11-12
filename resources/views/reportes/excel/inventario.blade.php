<table>
    <thead>
        <tr>
            <th colspan="7" style="font-weight: bold; font-size: 14px;">JP Vasquez Servicios Generales E.I.R.L. - REPORTE DE INVENTARIO</th>
        </tr>
        <tr>
            <th colspan="7" style="color: #555;">Fecha de Emisión: {{ $fecha_emision }} | Generado Por: {{ auth()->user()->name }}</th>
        </tr>
        <tr><th></th></tr>
        <tr>
            <th style="font-weight: bold; background-color: #e9ecef;">Código</th>
            <th style="font-weight: bold; background-color: #e9ecef;">Producto</th>
            <th style="font-weight: bold; background-color: #e9ecef;">Categoría</th>
            <th style="font-weight: bold; background-color: #e9ecef;">Unidad Medida</th>
            <th style="font-weight: bold; background-color: #e9ecef;">Stock Actual</th>
            <th style="font-weight: bold; background-color: #e9ecef;">Stock Mínimo</th>
            <th style="font-weight: bold; background-color: #e9ecef;">Estado</th>
        </tr>
    </thead>
    <tbody>
        @foreach($productos as $p)
            <tr>
                <td>{{ $p->codigo }}</td>
                <td>{{ $p->nombre }}</td>
                <td>{{ $p->categoria->nombre }}</td>
                <td>{{ $p->unidad_medida }}</td>
                <td>{{ $p->stock_actual }}</td>
                <td>{{ $p->stock_minimo }}</td>
                <td>
                    @if($p->stock_actual <= 0) Agotado
                    @elseif($p->stock_actual <= $p->stock_minimo) Stock Bajo
                    @else Normal @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
