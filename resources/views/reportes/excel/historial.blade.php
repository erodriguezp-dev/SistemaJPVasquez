<table>
    <thead>
        <tr>
            <th colspan="5" style="font-weight: bold; font-size: 14px;">JP Vasquez Servicios Generales E.I.R.L. - HISTORIAL DE GENERACIÓN DE REPORTES</th>
        </tr>
        <tr>
            <th colspan="5" style="color: #555;">Fecha de Emisión: {{ $fecha_emision }} | Generado Por: {{ auth()->user()->name }}</th>
        </tr>
        <tr><th></th></tr>
        <tr>
            <th style="font-weight: bold; background-color: #e9ecef;">Fecha y Hora</th>
            <th style="font-weight: bold; background-color: #e9ecef;">Tipo Reporte</th>
            <th style="font-weight: bold; background-color: #e9ecef;">Formato</th>
            <th style="font-weight: bold; background-color: #e9ecef;">Usuario Generador</th>
            <th style="font-weight: bold; background-color: #e9ecef;">Duración (seg)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($historial as $h)
            <tr>
                <td>{{ $h->hora_inicio?->format('d/m/Y H:i:s') }}</td>
                <td>{{ ucfirst($h->tipo) }}</td>
                <td>{{ strtoupper($h->formato) }}</td>
                <td>{{ $h->user->name }}</td>
                <td>{{ $h->duracion_segundos }}</td>
            </tr>
        @endforeach
        <tr><th></th></tr>
        <tr>
            <td colspan="5" style="font-weight: bold; background-color: #f8f9fa;">ESTADÍSTICAS DE RENDIMIENTO:</td>
        </tr>
        <tr>
            <td colspan="5">Promedio: {{ $stats['promedio'] }} seg | Mínimo: {{ $stats['minimo'] }} seg | Máximo: {{ $stats['maximo'] }} seg (Total registros: {{ $stats['total'] }})</td>
        </tr>
    </tbody>
</table>
