<?php

namespace App\Services;

use App\Models\MovimientoInventario;
use App\Models\Producto;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class InventarioService
{
    /**
     * Registra un movimiento de entrada de stock.
     */
    public function registrarEntrada(int $productoId, float $cantidad, string $motivo, int $userId, ?int $pedidoId = null, ?string $fecha = null): MovimientoInventario
    {
        return $this->procesarMovimiento($productoId, 'entrada', $cantidad, $motivo, $userId, $pedidoId, $fecha);
    }

    /**
     * Registra un movimiento de salida de stock validando disponibilidad.
     */
    public function registrarSalida(int $productoId, float $cantidad, string $motivo, int $userId, ?int $pedidoId = null, ?string $fecha = null): MovimientoInventario
    {
        return $this->procesarMovimiento($productoId, 'salida', $cantidad, $motivo, $userId, $pedidoId, $fecha);
    }

    /**
     * Registra un ajuste de stock a un valor físico determinado.
     */
    public function registrarAjuste(int $productoId, float $stockFisico, string $motivo, int $userId): MovimientoInventario
    {
        return DB::transaction(function () use ($productoId, $stockFisico, $motivo, $userId) {
            $producto = Producto::where('id', $productoId)->lockForUpdate()->firstOrFail();
            $stockAnterior = (float) $producto->stock_actual;

            $diferencia = $stockFisico - $stockAnterior;

            if (abs($diferencia) < 0.001) {
                throw new InvalidArgumentException('El stock físico coincide exactamente con el stock actual en sistema.');
            }

            $producto->stock_actual = $stockFisico;
            $producto->save();

            return MovimientoInventario::create([
                'producto_id' => $producto->id,
                'tipo' => 'ajuste',
                'cantidad' => abs($diferencia),
                'stock_anterior' => $stockAnterior,
                'stock_resultante' => $stockFisico,
                'motivo' => $motivo,
                'pedido_id' => null,
                'user_id' => $userId,
                'fecha_movimiento' => Carbon::now('America/Lima'),
            ]);
        });
    }

    /**
     * Procesa entradas o salidas dentro de una transacción con bloqueo pesimista.
     */
    private function procesarMovimiento(int $productoId, string $tipo, float $cantidad, string $motivo, int $userId, ?int $pedidoId = null, ?string $fecha = null): MovimientoInventario
    {
        if ($cantidad <= 0) {
            throw new InvalidArgumentException('La cantidad debe ser mayor a cero.');
        }

        return DB::transaction(function () use ($productoId, $tipo, $cantidad, $motivo, $userId, $pedidoId, $fecha) {
            $producto = Producto::where('id', $productoId)->lockForUpdate()->firstOrFail();
            $stockAnterior = (float) $producto->stock_actual;

            if ($tipo === 'salida') {
                if ($stockAnterior < $cantidad) {
                    $stockFormatted = number_format($stockAnterior, 2);
                    $cantFormatted = number_format($cantidad, 2);
                    throw new InvalidArgumentException("Stock insuficiente. El stock actual es {$stockFormatted}, no se pueden retirar {$cantFormatted}.");
                }
                $stockResultante = $stockAnterior - $cantidad;
            } else {
                $stockResultante = $stockAnterior + $cantidad;
            }

            $producto->stock_actual = $stockResultante;
            $producto->save();

            $fechaMovimiento = $fecha ? Carbon::parse($fecha, 'America/Lima') : Carbon::now('America/Lima');

            return MovimientoInventario::create([
                'producto_id' => $producto->id,
                'tipo' => $tipo,
                'cantidad' => $cantidad,
                'stock_anterior' => $stockAnterior,
                'stock_resultante' => $stockResultante,
                'motivo' => $motivo,
                'pedido_id' => $pedidoId,
                'user_id' => $userId,
                'fecha_movimiento' => $fechaMovimiento,
            ]);
        });
    }
}
