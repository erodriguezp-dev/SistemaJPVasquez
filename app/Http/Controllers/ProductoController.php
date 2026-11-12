<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductoRequest;
use App\Models\Categoria;
use App\Models\Producto;
use App\Services\InventarioService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductoController extends Controller
{
    /**
     * Lista de productos con filtros de búsqueda, categoría, estado y stock bajo.
     */
    public function index(Request $request): View
    {
        $buscar = trim((string) $request->input('buscar'));
        $categoriaId = $request->input('categoria_id');
        $estado = $request->input('estado');
        $soloBajo = $request->boolean('stock_bajo');

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

        if ($estado === '1' || $estado === '0') {
            $query->where('activo', (bool) $estado);
        }

        if ($soloBajo) {
            $query->whereColumn('stock_actual', '<=', 'stock_minimo');
        }

        $productos = $query->orderBy('nombre', 'asc')->paginate(12)->withQueryString();
        $categorias = Categoria::orderBy('nombre', 'asc')->get();

        return view('productos.index', compact('productos', 'categorias', 'buscar', 'categoriaId', 'estado', 'soloBajo'));
    }

    /**
     * Muestra formulario para registrar un nuevo producto.
     */
    public function create(): View
    {
        $categorias = Categoria::orderBy('nombre', 'asc')->get();
        return view('productos.create', compact('categorias'));
    }

    /**
     * Almacena un producto y registra el stock inicial si es mayor a cero.
     */
    public function store(ProductoRequest $request, InventarioService $inventarioService): RedirectResponse
    {
        $validated = $request->validated();
        $stockInicial = (float) ($validated['stock_inicial'] ?? 0);
        unset($validated['stock_inicial']);

        $validated['stock_actual'] = 0.00;
        $validated['activo'] = true;

        $producto = Producto::create($validated);

        if ($stockInicial > 0) {
            $inventarioService->registrarEntrada(
                $producto->id,
                $stockInicial,
                'Stock inicial de apertura de producto',
                auth()->id()
            );
        }

        return redirect()->route('productos.index')->with('success', "Producto '{$producto->nombre}' creado correctamente.");
    }

    /**
     * Muestra el detalle de un producto.
     */
    public function show(Producto $producto): View
    {
        $producto->load('categoria');
        return view('productos.show', compact('producto'));
    }

    /**
     * Muestra formulario para editar un producto.
     */
    public function edit(Producto $producto): View
    {
        $categorias = Categoria::orderBy('nombre', 'asc')->get();
        return view('productos.edit', compact('producto', 'categorias'));
    }

    /**
     * Actualiza la información básica del producto (sin alterar stock_actual directamente).
     */
    public function update(ProductoRequest $request, Producto $producto): RedirectResponse
    {
        $validated = $request->validated();
        unset($validated['stock_inicial']);

        $producto->update($validated);

        return redirect()->route('productos.index')->with('success', "Producto '{$producto->nombre}' actualizado correctamente.");
    }

    /**
     * Activa o desactiva un producto.
     */
    public function toggleStatus(Producto $producto): RedirectResponse
    {
        $producto->activo = ! $producto->activo;
        $producto->save();

        $estadoTexto = $producto->activo ? 'activado' : 'desactivado';

        return redirect()->route('productos.index')->with('success', "Producto '{$producto->codigo}' {$estadoTexto} correctamente.");
    }
}
