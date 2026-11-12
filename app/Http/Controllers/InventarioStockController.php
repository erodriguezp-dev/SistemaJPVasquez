<?php

namespace App\Http\Controllers;

use App\Http\Requests\AjusteRequest;
use App\Models\Categoria;
use App\Models\Producto;
use App\Services\InventarioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class InventarioStockController extends Controller
{
    /**
     * Pantalla de consulta de stock en tiempo real con resúmenes y alertas.
     */
    public function stock(Request $request): View
    {
        $buscar = trim((string) $request->input('buscar'));
        $categoriaId = $request->input('categoria_id');
        $estadoStock = $request->input('estado_stock'); // 'normal', 'bajo', 'agotado', 'alerta'

        $query = Producto::with('categoria');

        if ($buscar !== '') {
            $query->where(function ($q) use ($buscar) {
                $q->where('codigo', 'LIKE', "%{$buscar}%")
                  ->orWhere('nombre', 'LIKE', "%{$buscar}%");
            });
        }

        if (! empty($categoriaId)) {
            $query->where('categoria_id', $categoriaId);
        }

        if ($estadoStock === 'normal') {
            $query->whereColumn('stock_actual', '>', 'stock_minimo');
        } elseif ($estadoStock === 'bajo') {
            $query->where('stock_actual', '>', 0)->whereColumn('stock_actual', '<=', 'stock_minimo');
        } elseif ($estadoStock === 'agotado') {
            $query->where('stock_actual', '<=', 0);
        } elseif ($estadoStock === 'alerta') {
            $query->whereColumn('stock_actual', '<=', 'stock_minimo');
        }

        $productos = $query->orderBy('nombre', 'asc')->paginate(15)->withQueryString();

        // Conteos generales para las tarjetas de resumen
        $totalProductos = Producto::where('activo', true)->count();
        $totalStockBajo = Producto::where('activo', true)->where('stock_actual', '>', 0)->whereColumn('stock_actual', '<=', 'stock_minimo')->count();
        $totalAgotados = Producto::where('activo', true)->where('stock_actual', '<=', 0)->count();
        $totalAlertas = $totalStockBajo + $totalAgotados;

        $categorias = Categoria::orderBy('nombre', 'asc')->get();

        return view('inventario.stock', compact(
            'productos', 'categorias', 'buscar', 'categoriaId', 'estadoStock',
            'totalProductos', 'totalStockBajo', 'totalAgotados', 'totalAlertas'
        ));
    }

    /**
     * Muestra el formulario para realizar un ajuste de inventario por conteo físico.
     */
    public function createAjuste(Request $request): View
    {
        $productos = Producto::where('activo', true)->orderBy('nombre', 'asc')->get();
        $productoSeleccionadoId = $request->input('producto_id');

        return view('inventario.ajuste', compact('productos', 'productoSeleccionadoId'));
    }

    /**
     * Procesa y registra el ajuste de inventario mediante InventarioService.
     */
    public function storeAjuste(AjusteRequest $request, InventarioService $inventarioService): RedirectResponse
    {
        $v = $request->validated();

        try {
            $inventarioService->registrarAjuste(
                (int) $v['producto_id'],
                (float) $v['stock_fisico'],
                $v['motivo'],
                auth()->id()
            );

            return redirect()->route('inventario.stock')->with('success', 'Ajuste de inventario registrado correctamente.');
        } catch (InvalidArgumentException $e) {
            return back()->withInput()->with('warning', $e->getMessage());
        }
    }
}
