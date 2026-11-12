<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\ConstructionController;
use App\Http\Controllers\InventarioStockController;
use App\Http\Controllers\MovimientoController;
use App\Http\Controllers\PedidoController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas Públicas (Autenticación)
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

/*
|--------------------------------------------------------------------------
| Rutas Protegidas por Autenticación
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/', function () { return view('home'); })->name('home');

    // Gestión de Usuarios (Exclusivo Administrador)
    Route::middleware('role:administrador')->group(function () {
        Route::resource('usuarios', UserController::class)->except(['show', 'destroy']);
        Route::patch('/usuarios/{usuario}/toggle-status', [UserController::class, 'toggleStatus'])->name('usuarios.toggle-status');
    });

    // Módulo de Inventario (Consulta de Stock y Kardex)
    Route::middleware('role:administrador,gerente_proyecto,contador,colaborador')->group(function () {
        Route::get('/inventario', [InventarioStockController::class, 'stock'])->name('inventario.stock');
        Route::get('/inventario/stock', [InventarioStockController::class, 'stock']);
        Route::get('/inventario/kardex/{producto}', [MovimientoController::class, 'kardex'])->name('inventario.kardex');
        Route::get('/movimientos', [MovimientoController::class, 'index'])->name('movimientos.index');
        Route::get('/productos', [ProductoController::class, 'index'])->name('productos.index');
        Route::get('/productos/{producto}', [ProductoController::class, 'show'])->name('productos.show');
        Route::get('/categorias', [CategoriaController::class, 'index'])->name('categorias.index');

        // Módulo de Pedidos - Consulta y Detalle
        Route::get('/pedidos', [PedidoController::class, 'index'])->name('pedidos.index');
        Route::get('/pedidos/{pedido}', [PedidoController::class, 'show'])->name('pedidos.show');
    });

    // Operaciones de Pedidos (Registro y Avance/Retroceso de Estado)
    Route::middleware('role:administrador,gerente_proyecto,colaborador')->group(function () {
        Route::get('/pedidos/crear/nuevo', [PedidoController::class, 'create'])->name('pedidos.create');
        Route::post('/pedidos', [PedidoController::class, 'store'])->name('pedidos.store');
        Route::post('/pedidos/{pedido}/cambiar-estado', [PedidoController::class, 'cambiarEstado'])->name('pedidos.cambiar-estado');
    });

    // Operaciones de Pedidos (Edición de Datos de Cliente por Admin / Gerente)
    Route::middleware('role:administrador,gerente_proyecto')->group(function () {
        Route::get('/pedidos/{pedido}/editar', [PedidoController::class, 'edit'])->name('pedidos.edit');
        Route::put('/pedidos/{pedido}', [PedidoController::class, 'update'])->name('pedidos.update');
    });

    // Operaciones de Inventario (Alta/Edición de Productos, Movimientos y Ajustes)
    Route::middleware('role:administrador,colaborador')->group(function () {
        Route::get('/productos/crear/nuevo', [ProductoController::class, 'create'])->name('productos.create');
        Route::post('/productos', [ProductoController::class, 'store'])->name('productos.store');
        Route::get('/productos/{producto}/editar', [ProductoController::class, 'edit'])->name('productos.edit');
        Route::put('/productos/{producto}', [ProductoController::class, 'update'])->name('productos.update');
        Route::patch('/productos/{producto}/toggle-status', [ProductoController::class, 'toggleStatus'])->name('productos.toggle-status');

        Route::get('/movimientos/crear', [MovimientoController::class, 'create'])->name('movimientos.create');
        Route::post('/movimientos', [MovimientoController::class, 'store'])->name('movimientos.store');
        Route::get('/inventario/ajuste', [InventarioStockController::class, 'createAjuste'])->name('inventario.ajuste.create');
        Route::post('/inventario/ajuste', [InventarioStockController::class, 'storeAjuste'])->name('inventario.ajuste.store');
    });

    // Categorías (Operaciones Administrador)
    Route::middleware('role:administrador')->group(function () {
        Route::post('/categorias', [CategoriaController::class, 'store'])->name('categorias.store');
        Route::put('/categorias/{categoria}', [CategoriaController::class, 'update'])->name('categorias.update');
        Route::delete('/categorias/{categoria}', [CategoriaController::class, 'destroy'])->name('categorias.destroy');
    });

    // Módulo de Reportes (Administrador, Gerente de Proyecto, Contador)
    Route::middleware('role:administrador,gerente_proyecto,contador')->group(function () {
        Route::get('/reportes', [ReporteController::class, 'dashboard'])->name('reportes.dashboard');
        Route::get('/reportes/dashboard', [ReporteController::class, 'dashboard']);
        Route::get('/reportes/generar', [ReporteController::class, 'index'])->name('reportes.index');
        Route::post('/reportes/generar', [ReporteController::class, 'generar'])->name('reportes.generar');
        Route::get('/reportes/historial', [ReporteController::class, 'historial'])->name('reportes.historial');
        Route::get('/reportes/historial/excel', [ReporteController::class, 'exportarHistorial'])->name('reportes.historial.excel');
    });

    Route::get('/modulo/{modulo}', [ConstructionController::class, 'show'])->name('construction');
});


