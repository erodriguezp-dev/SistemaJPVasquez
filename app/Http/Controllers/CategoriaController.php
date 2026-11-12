<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoriaRequest;
use App\Models\Categoria;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoriaController extends Controller
{
    /**
     * Muestra la lista de categorías con cantidad de productos.
     */
    public function index(): View
    {
        $categorias = Categoria::withCount('productos')->orderBy('nombre', 'asc')->paginate(10);
        return view('categorias.index', compact('categorias'));
    }

    /**
     * Almacena una nueva categoría.
     */
    public function store(CategoriaRequest $request): RedirectResponse
    {
        Categoria::create($request->validated());
        return redirect()->route('categorias.index')->with('success', 'Categoría registrada correctamente.');
    }

    /**
     * Actualiza una categoría existente.
     */
    public function update(CategoriaRequest $request, Categoria $categoria): RedirectResponse
    {
        $categoria->update($request->validated());
        return redirect()->route('categorias.index')->with('success', 'Categoría actualizada correctamente.');
    }

    /**
     * Elimina una categoría si no tiene productos vinculados.
     */
    public function destroy(Categoria $categoria): RedirectResponse
    {
        $cantProductos = $categoria->productos()->count();

        if ($cantProductos > 0) {
            return back()->with('error', "No se puede eliminar la categoría '{$categoria->nombre}' porque tiene {$cantProductos} productos asociados.");
        }

        $categoria->delete();

        return redirect()->route('categorias.index')->with('success', 'Categoría eliminada correctamente.');
    }
}
