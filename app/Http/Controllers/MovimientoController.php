<?php

namespace App\Http\Controllers;

use App\Http\Requests\MovimientoRequest;
use App\Models\MovimientoInventario;
use App\Models\Producto;
use App\Services\InventarioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class MovimientoController extends Controller
{
    /**
     * Listado general de movimientos de inventario con filtros.
     */
    public function index(Request $request): View
    {
        $productoId = $request->input('producto_id');
        $tipo = $request->input('tipo');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        $query = MovimientoInventario::with(['producto.categoria', 'user', 'pedido']);

        if (! empty($productoId)) {
            $query->where('producto_id', $productoId);
        }

        if (! empty($tipo) && in_array($tipo, ['entrada', 'salida', 'ajuste'], true)) {
            $query->where('tipo', $tipo);
        }

        if (! empty($fechaInicio)) {
            $query->whereDate('fecha_movimiento', '>=', $fechaInicio);
        }

        if (! empty($fechaFin)) {
            $query->whereDate('fecha_movimiento', '<=', $fechaFin);
        }

        $movimientos = $query->orderBy('fecha_movimiento', 'desc')->orderBy('id', 'desc')->paginate(15)->withQueryString();
        $productos = Producto::orderBy('nombre', 'asc')->get();

        return view('movimientos.index', compact('movimientos', 'productos', 'productoId', 'tipo', 'fechaInicio', 'fechaFin'));
    }

    /**
     * Formulario para registrar una entrada o salida.
     */
    public function create(Request $request): View
    {
        $productos = Producto::where('activo', true)->orderBy('nombre', 'asc')->get();
        $productoSeleccionadoId = $request->input('producto_id');

        return view('movimientos.create', compact('productos', 'productoSeleccionadoId'));
    }

    /**
     * Procesa y registra la entrada o salida a través de InventarioService.
     */
    public function store(MovimientoRequest $request, InventarioService $inventarioService): RedirectResponse
    {
        $v = $request->validated();

        try {
            if ($v['tipo'] === 'entrada') {
                $inventarioService->registrarEntrada(
                    (int) $v['producto_id'],
                    (float) $v['cantidad'],
                    $v['motivo'],
                    auth()->id(),
                    null,
                    $v['fecha_movimiento'] ?? null
                );
            } else {
                $inventarioService->registrarSalida(
                    (int) $v['producto_id'],
                    (float) $v['cantidad'],
                    $v['motivo'],
                    auth()->id(),
                    null,
                    $v['fecha_movimiento'] ?? null
                );
            }

            return redirect()->route('movimientos.index')->with('success', 'Movimiento registrado correctamente.');
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->withErrors(['cantidad' => $e->getMessage()]);
        }
    }

    /**
     * Muestra el Kardex (historial de movimientos) de un producto específico.
     */
    public function kardex(Producto $producto): View
    {
        $movimientos = $producto->movimientosInventario()
            ->with(['user', 'pedido'])
            ->orderBy('fecha_movimiento', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20);

        return view('inventario.kardex', compact('producto', 'movimientos'));
    }
}
