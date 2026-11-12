<?php

namespace App\Services;

use App\Models\DetallePedido;
use App\Models\MovimientoInventario;
use App\Models\Pedido;
use App\Models\Producto;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function resolverRangoFechas(string $periodo, ?string $fechaInicio, ?string $fechaFin): array
    {
        $now = Carbon::now('America/Lima');
        switch ($periodo) {
            case 'hoy':
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                break;
            case '7_dias':
                $start = $now->copy()->subDays(7)->startOfDay();
                $end = $now->copy()->endOfDay();
                break;
            case 'este_mes':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
            case 'personalizado':
                $start = ! empty($fechaInicio) ? Carbon::parse($fechaInicio, 'America/Lima')->startOfDay() : $now->copy()->subDays(30)->startOfDay();
                $end = ! empty($fechaFin) ? Carbon::parse($fechaFin, 'America/Lima')->endOfDay() : $now->copy()->endOfDay();
                break;
            case '30_dias':
            default:
                $periodo = '30_dias';
                $start = $now->copy()->subDays(30)->startOfDay();
                $end = $now->copy()->endOfDay();
                break;
        }

        return ['start' => $start, 'end' => $end, 'dias' => max(1, $start->diffInDays($end)), 'periodo' => $periodo];
    }

    public function obtenerDatosDashboard(string $periodo, ?string $fechaInicio, ?string $fechaFin): array
    {
        $rango = $this->resolverRangoFechas($periodo, $fechaInicio, $fechaFin);
        $start = $rango['start'];
        $end = $rango['end'];

        $kpis = [
            'pedidos_registrados' => Pedido::whereBetween('fecha_pedido', [$start, $end])->count(),
            'pedidos_completados' => Pedido::whereBetween('fecha_pedido', [$start, $end])->whereIn('estado', ['entregado', 'confirmado'])->count(),
            'unidades_despachadas' => (float) (MovimientoInventario::whereBetween('fecha_movimiento', [$start, $end])->where('tipo', 'salida')->whereNotNull('pedido_id')->sum('cantidad') ?? 0),
            'productos_alerta' => Producto::where('activo', true)->whereColumn('stock_actual', '<=', 'stock_minimo')->count(),
        ];

        $topData = DetallePedido::join('pedidos', 'detalle_pedido.pedido_id', '=', 'pedidos.id')
            ->join('productos', 'detalle_pedido.producto_id', '=', 'productos.id')
            ->whereBetween('pedidos.fecha_pedido', [$start, $end])
            ->whereNotNull('detalle_pedido.producto_id')
            ->select('productos.nombre', DB::raw('SUM(detalle_pedido.cantidad) as total_vendido'))
            ->groupBy('productos.id', 'productos.nombre')->orderByDesc('total_vendido')->limit(10)->get();

        $chartTopProductos = ['labels' => $topData->pluck('nombre')->toArray(), 'values' => $topData->pluck('total_vendido')->map(fn ($v) => (float) $v)->toArray()];

        $estadosMap = ['recibido' => 'Recibido', 'en_proceso' => 'En Proceso', 'terminado' => 'Terminado', 'entregado' => 'Entregado', 'confirmado' => 'Confirmado'];
        $colorsMap = ['recibido' => '#6c757d', 'en_proceso' => '#0d6efd', 'terminado' => '#0dcaf0', 'entregado' => '#ffc107', 'confirmado' => '#198754'];
        $pedidosEstadoData = Pedido::whereBetween('fecha_pedido', [$start, $end])->select('estado', DB::raw('COUNT(*) as total'))->groupBy('estado')->pluck('total', 'estado')->toArray();

        $chartEstados = ['labels' => [], 'values' => [], 'colors' => []];
        foreach ($estadosMap as $key => $label) {
            $val = (int) ($pedidosEstadoData[$key] ?? 0);
            if ($val > 0) {
                $chartEstados['labels'][] = $label;
                $chartEstados['values'][] = $val;
                $chartEstados['colors'][] = $colorsMap[$key];
            }
        }

        $tiposMap = ['mueble' => 'Mueble', 'ferreteria' => 'Ferretería', 'reparacion' => 'Reparación'];
        $pedidosTipoData = Pedido::whereBetween('fecha_pedido', [$start, $end])->select('tipo', DB::raw('COUNT(*) as total'))->groupBy('tipo')->pluck('total', 'tipo')->toArray();
        $chartTipos = ['labels' => [], 'values' => []];
        foreach ($tiposMap as $key => $label) {
            $chartTipos['labels'][] = $label;
            $chartTipos['values'][] = (int) ($pedidosTipoData[$key] ?? 0);
        }

        $groupbyFormat = $rango['dias'] > 90 ? '%Y-%m' : '%Y-%m-%d';
        $evolucionData = Pedido::whereBetween('fecha_pedido', [$start, $end])
            ->select(DB::raw("DATE_FORMAT(fecha_pedido, '{$groupbyFormat}') as periodo_group"), DB::raw('COUNT(*) as total'))
            ->groupBy('periodo_group')->orderBy('periodo_group', 'asc')->pluck('total', 'periodo_group')->toArray();

        $chartEvolucion = ['labels' => array_keys($evolucionData), 'values' => array_values(array_map('intval', $evolucionData))];

        $mesesAbrev = [
            1 => 'Ene', 2 => 'Feb', 3 => 'Mar', 4 => 'Abr', 5 => 'May', 6 => 'Jun',
            7 => 'Jul', 8 => 'Ago', 9 => 'Set', 10 => 'Oct', 11 => 'Nov', 12 => 'Dic',
        ];

        if ($rango['dias'] > 90) {
            $movsData = MovimientoInventario::whereBetween('fecha_movimiento', [$start, $end])
                ->select(DB::raw("DATE_FORMAT(fecha_movimiento, '%Y-%m') as periodo_group"), 'tipo', DB::raw('SUM(cantidad) as total'))
                ->groupBy('periodo_group', 'tipo')->orderBy('periodo_group', 'asc')->get();

            $periodos = $movsData->pluck('periodo_group')->unique()->sort()->values();
            $entradas = []; $salidas = []; $labels = [];
            foreach ($periodos as $p) {
                $partes = explode('-', (string) $p);
                $mesNum = (int) ($partes[1] ?? 1);
                $mesNom = $mesesAbrev[$mesNum] ?? ($partes[1] ?? '');
                $labels[] = "{$mesNom} {$partes[0]}";
                $entradas[] = (float) ($movsData->where('periodo_group', $p)->where('tipo', 'entrada')->first()?->total ?? 0);
                $salidas[] = (float) ($movsData->where('periodo_group', $p)->where('tipo', 'salida')->first()?->total ?? 0);
            }
            $chartMovimientos = ['labels' => $labels, 'entradas' => $entradas, 'salidas' => $salidas];
        } else {
            $movsData = MovimientoInventario::whereBetween('fecha_movimiento', [$start, $end])
                ->select(DB::raw("YEARWEEK(fecha_movimiento, 1) as semana"), 'tipo', DB::raw('SUM(cantidad) as total'))
                ->groupBy('semana', 'tipo')->orderBy('semana', 'asc')->get();

            $semanas = $movsData->pluck('semana')->unique()->sort()->values();
            $entradasSemana = []; $salidasSemana = []; $labelsSemanas = [];
            foreach ($semanas as $sem) {
                $year = (int) substr((string) $sem, 0, 4);
                $week = (int) substr((string) $sem, 4);
                $d = Carbon::now('America/Lima')->setISODate($year, $week);
                $mesNom = $mesesAbrev[$d->month] ?? $d->format('M');
                $semanaDelMes = (int) ceil($d->day / 7);

                $labelsSemanas[] = "{$mesNom}. Semana {$semanaDelMes}";
                $entradasSemana[] = (float) ($movsData->where('semana', $sem)->where('tipo', 'entrada')->first()?->total ?? 0);
                $salidasSemana[] = (float) ($movsData->where('semana', $sem)->where('tipo', 'salida')->first()?->total ?? 0);
            }
            $chartMovimientos = ['labels' => $labelsSemanas, 'entradas' => $entradasSemana, 'salidas' => $salidasSemana];
        }

        return compact('rango', 'kpis', 'chartTopProductos', 'chartEstados', 'chartTipos', 'chartEvolucion', 'chartMovimientos');
    }
}

