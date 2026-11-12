<?php

namespace App\Http\Controllers;

use App\Http\Requests\CambioEstadoRequest;
use App\Http\Requests\PedidoStoreRequest;
use App\Http\Requests\PedidoUpdateRequest;
use App\Models\Pedido;
use App\Models\Producto;
use App\Services\PedidoService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use InvalidArgumentException;

class PedidoController extends Controller
{
    public function index(Request $request): View
    {
        $buscar = trim((string) $request->input('buscar'));
        $estado = $request->input('estado');
        $tipo = $request->input('tipo');
        $fechaInicio = $request->input('fecha_inicio');
        $fechaFin = $request->input('fecha_fin');

        $query = Pedido::with('user');

        if ($buscar !== '') {
            $query->where(function ($q) use ($buscar) {
                $q->where('codigo', 'LIKE', "%{$buscar}%")
                  ->orWhere('cliente_nombre', 'LIKE', "%{$buscar}%");
            });
        }

        if (! empty($estado)) {
            $query->where('estado', $estado);
        }

        if (! empty($tipo)) {
            $query->where('tipo', $tipo);
        }

        if (! empty($fechaInicio)) {
            $query->whereDate('fecha_pedido', '>=', $fechaInicio);
        }

        if (! empty($fechaFin)) {
            $query->whereDate('fecha_pedido', '<=', $fechaFin);
        }

        $pedidos = $query->orderBy('id', 'desc')->paginate(12)->withQueryString();

        return view('pedidos.index', compact('pedidos', 'buscar', 'estado', 'tipo', 'fechaInicio', 'fechaFin'));
    }

    public function create(): View
    {
        $productos = Producto::where('activo', true)
            ->orderBy('nombre', 'asc')
            ->get(['id', 'codigo', 'nombre', 'stock_actual', 'unidad_medida', 'precio_referencial']);

        return view('pedidos.create', compact('productos'));
    }

    public function store(PedidoStoreRequest $request, PedidoService $pedidoService): RedirectResponse
    {
        try {
            $pedido = $pedidoService->crearPedido($request->validated(), auth()->id());
            return redirect()->route('pedidos.show', $pedido)->with('success', "Pedido {$pedido->codigo} registrado correctamente.");
        } catch (InvalidArgumentException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(Pedido $pedido): View
    {
        $pedido->load([
            'detalles.producto',
            'user',
            'historialEstados.user',
            'movimientosInventario.producto',
            'movimientosInventario.user',
        ]);

        return view('pedidos.show', compact('pedido'));
    }

    public function edit(Pedido $pedido): View
    {
        $pedido->load('detalles.producto');
        return view('pedidos.edit', compact('pedido'));
    }

    public function update(PedidoUpdateRequest $request, Pedido $pedido, PedidoService $pedidoService): RedirectResponse
    {
        try {
            $pedidoService->actualizarPedido($pedido, $request->validated(), auth()->id());
            return redirect()->route('pedidos.show', $pedido)->with('success', 'Pedido actualizado correctamente.');
        } catch (InvalidArgumentException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }


    public function cambiarEstado(CambioEstadoRequest $request, Pedido $pedido, PedidoService $pedidoService): RedirectResponse
    {
        $pedidoService->cambiarEstado(
            $pedido,
            $request->input('nuevo_estado'),
            auth()->id(),
            $request->input('observacion')
        );

        return redirect()->route('pedidos.show', $pedido)->with('success', 'Estado del pedido actualizado correctamente.');
    }
}
