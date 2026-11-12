<?php

namespace App\Services;

use App\Models\Pedido;
use App\Models\Producto;
use App\Models\ReporteGenerado;
use Carbon\Carbon;

class ReporteService
{
    /**
     * Obtiene los datos estructurados para el reporte de inventario.
     */
    public function obtenerDatosInventario(array $filtros): array
    {
        $query = Producto::with('categoria');

        if (! empty($filtros['categoria_id'])) {
            $query->where('categoria_id', $filtros['categoria_id']);
        }

        if (! empty($filtros['producto_id'])) {
            $query->where('id', $filtros['producto_id']);
        }

        if (! empty($filtros['estado_stock'])) {
            if ($filtros['estado_stock'] === 'agotado') {
                $query->where('stock_actual', '<=', 0);
            } elseif ($filtros['estado_stock'] === 'bajo') {
                $query->where('stock_actual', '>', 0)->whereColumn('stock_actual', '<=', 'stock_minimo');
            } elseif ($filtros['estado_stock'] === 'normal') {
                $query->whereColumn('stock_actual', '>', 'stock_minimo');
            }
        }

        $incluirMovs = ! empty($filtros['incluir_movimientos']);
        if ($incluirMovs) {
            $query->with(['movimientosInventario' => function ($q) use ($filtros) {
                if (! empty($filtros['fecha_inicio'])) {
                    $q->whereDate('fecha_movimiento', '>=', $filtros['fecha_inicio']);
                }
                if (! empty($filtros['fecha_fin'])) {
                    $q->whereDate('fecha_movimiento', '<=', $filtros['fecha_fin']);
                }
                $q->with('user')->orderBy('fecha_movimiento', 'desc');
            }]);
        }

        $productos = $query->orderBy('nombre', 'asc')->get();

        return [
            'productos' => $productos,
            'filtros' => $filtros,
            'incluir_movimientos' => $incluirMovs,
            'fecha_emision' => Carbon::now('America/Lima')->format('d/m/Y H:i:s'),
        ];
    }

    /**
     * Obtiene los datos estructurados para el reporte de pedidos con resúmenes.
     */
    public function obtenerDatosPedidos(array $filtros): array
    {
        $query = Pedido::with(['user', 'detalles']);

        if (! empty($filtros['tipo_pedido'])) {
            $query->where('tipo', $filtros['tipo_pedido']);
        }

        if (! empty($filtros['estado_pedido'])) {
            $query->where('estado', $filtros['estado_pedido']);
        }

        if (! empty($filtros['fecha_inicio'])) {
            $query->whereDate('fecha_pedido', '>=', $filtros['fecha_inicio']);
        }

        if (! empty($filtros['fecha_fin'])) {
            $query->whereDate('fecha_pedido', '<=', $filtros['fecha_fin']);
        }

        $pedidos = $query->orderBy('id', 'desc')->get();

        // Calcular resúmenes
        $totalesEstado = [
            'recibido' => 0, 'en_proceso' => 0, 'terminado' => 0, 'entregado' => 0, 'confirmado' => 0,
        ];
        $totalesTipo = [
            'mueble' => 0, 'ferreteria' => 0, 'reparacion' => 0,
        ];

        foreach ($pedidos as $p) {
            if (isset($totalesEstado[$p->estado])) {
                $totalesEstado[$p->estado]++;
            }
            if (isset($totalesTipo[$p->tipo])) {
                $totalesTipo[$p->tipo]++;
            }
        }

        return [
            'pedidos' => $pedidos,
            'totales_estado' => $totalesEstado,
            'totales_tipo' => $totalesTipo,
            'filtros' => $filtros,
            'fecha_emision' => Carbon::now('America/Lima')->format('d/m/Y H:i:s'),
        ];
    }

    /**
     * Registra la medición de tiempo en reportes_generados y devuelve la duración en segundos.
     */
    public function registrarMedicion(string $tipo, array $filtros, string $formato, int $userId, float $startMicrotime): float
    {
        $endMicrotime = microtime(true);
        $duracion = round($endMicrotime - $startMicrotime, 3);
        if ($duracion <= 0) {
            $duracion = 0.001;
        }

        ReporteGenerado::create([
            'tipo' => $tipo,
            'filtros' => $filtros,
            'formato' => $formato,
            'hora_inicio' => Carbon::createFromTimestamp($startMicrotime, 'America/Lima'),
            'hora_fin' => Carbon::createFromTimestamp($endMicrotime, 'America/Lima'),
            'duracion_segundos' => $duracion,
            'user_id' => $userId,
        ]);

        return $duracion;
    }

    /**
     * Obtiene las estadísticas de duración (Promedio, Mínimo, Máximo) para el historial.
     */
    public function obtenerEstadisticasHistorial(array $filtros): array
    {
        $query = ReporteGenerado::query();

        if (! empty($filtros['tipo'])) {
            $query->where('tipo', $filtros['tipo']);
        }
        if (! empty($filtros['fecha_inicio'])) {
            $query->whereDate('hora_inicio', '>=', $filtros['fecha_inicio']);
        }
        if (! empty($filtros['fecha_fin'])) {
            $query->whereDate('hora_inicio', '<=', $filtros['fecha_fin']);
        }

        return [
            'total' => (clone $query)->count(),
            'promedio' => round((float) ((clone $query)->avg('duracion_segundos') ?? 0), 3),
            'minimo' => round((float) ((clone $query)->min('duracion_segundos') ?? 0), 3),
            'maximo' => round((float) ((clone $query)->max('duracion_segundos') ?? 0), 3),
        ];
    }
}
