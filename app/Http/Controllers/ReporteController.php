<?php

namespace App\Http\Controllers;

use App\Exports\HistorialReportesExport;
use App\Exports\ReporteInventarioExport;
use App\Exports\ReportePedidosExport;
use App\Http\Requests\ReporteRequest;
use App\Models\Categoria;
use App\Models\Producto;
use App\Models\ReporteGenerado;
use App\Services\DashboardService;
use App\Services\ReporteService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class ReporteController extends Controller
{
    public function dashboard(Request $request, DashboardService $dashboardService): View
    {
        $periodo = $request->input('periodo', '30_dias');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        $data = $dashboardService->obtenerDatosDashboard($periodo, $fechaInicio, $fechaFin);

        return view('reportes.dashboard', $data);
    }

    public function index(): View
    {
        $categorias = Categoria::orderBy('nombre', 'asc')->get();
        $productos = Producto::where('activo', true)->orderBy('nombre', 'asc')->get();

        return view('reportes.index', compact('categorias', 'productos'));
    }

    public function generar(ReporteRequest $request, ReporteService $reporteService)
    {
        $startMicrotime = microtime(true);
        $filtros = $request->validated();
        $tipo = $filtros['tipo'];
        $formato = $filtros['formato'];

        if ($tipo === 'inventario') {
            $data = $reporteService->obtenerDatosInventario($filtros);
            $exportClass = ReporteInventarioExport::class;
            $viewPdf = 'reportes.pdf.inventario';
        } else {
            $data = $reporteService->obtenerDatosPedidos($filtros);
            $exportClass = ReportePedidosExport::class;
            $viewPdf = 'reportes.pdf.pedidos';
        }

        $duracion = $reporteService->registrarMedicion($tipo, $filtros, $formato, auth()->id(), $startMicrotime);
        $data['duracion_segundos'] = $duracion;

        if ($formato === 'pantalla') {
            $categorias = Categoria::orderBy('nombre', 'asc')->get();
            $productos = Producto::where('activo', true)->orderBy('nombre', 'asc')->get();
            session()->flash('success', "Reporte generado en {$duracion} segundos.");

            return view('reportes.index', compact('categorias', 'productos', 'data', 'duracion'));
        }

        if ($formato === 'pdf') {
            $pdf = Pdf::loadView($viewPdf, $data);
            $pdf->setPaper('a4', 'landscape');

            return $pdf->download("reporte_{$tipo}_".now()->format('Ymd_His').".pdf");
        }

        return Excel::download(new $exportClass($data), "reporte_{$tipo}_".now()->format('Ymd_His').".xlsx");
    }

    public function historial(Request $request, ReporteService $reporteService): View
    {
        $tipo = $request->input('tipo');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        $query = ReporteGenerado::with('user');

        if (! empty($tipo)) {
            $query->where('tipo', $tipo);
        }
        if (! empty($fechaInicio)) {
            $query->whereDate('hora_inicio', '>=', $fechaInicio);
        }
        if (! empty($fechaFin)) {
            $query->whereDate('hora_inicio', '<=', $fechaFin);
        }

        $historial = $query->orderBy('id', 'desc')->paginate(15)->withQueryString();
        $stats = $reporteService->obtenerEstadisticasHistorial(compact('tipo', 'fechaInicio', 'fechaFin'));

        return view('reportes.historial', compact('historial', 'stats', 'tipo', 'fechaInicio', 'fechaFin'));
    }

    public function exportarHistorial(Request $request, ReporteService $reporteService)
    {
        $tipo = $request->input('tipo');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        $query = ReporteGenerado::with('user');

        if (! empty($tipo)) {
            $query->where('tipo', $tipo);
        }
        if (! empty($fechaInicio)) {
            $query->whereDate('hora_inicio', '>=', $fechaInicio);
        }
        if (! empty($fechaFin)) {
            $query->whereDate('hora_inicio', '<=', $fechaFin);
        }

        $registros = $query->orderBy('id', 'desc')->get();
        $stats = $reporteService->obtenerEstadisticasHistorial(compact('tipo', 'fechaInicio', 'fechaFin'));

        $data = [
            'historial' => $registros,
            'stats' => $stats,
            'fecha_emision' => now()->format('d/m/Y H:i:s'),
        ];

        return Excel::download(new HistorialReportesExport($data), 'historial_reportes_'.now()->format('Ymd_His').'.xlsx');
    }
}
