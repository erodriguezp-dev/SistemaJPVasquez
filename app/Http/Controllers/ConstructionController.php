<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ConstructionController extends Controller
{
    /**
     * Muestra la pantalla de módulo en construcción para secciones pendientes.
     */
    public function show(string $modulo): View
    {
        $titulos = [
            'inventario' => 'Módulo de Inventario',
            'pedidos' => 'Módulo de Pedidos',
            'reportes' => 'Módulo de Reportes',
        ];

        $nombreModulo = $titulos[$modulo] ?? 'Módulo del Sistema';

        return view('construction', compact('nombreModulo', 'modulo'));
    }
}
