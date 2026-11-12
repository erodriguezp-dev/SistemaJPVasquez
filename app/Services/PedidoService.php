<?php

namespace App\Services;

use App\Models\DetallePedido;
use App\Models\HistorialEstadoPedido;
use App\Models\Pedido;
use App\Models\Producto;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PedidoService
{
    public function __construct(
        protected InventarioService $inventarioService
    ) {}

    /**
     * Genera el siguiente código correlativo de pedido con formato PED-AAAA-0001.
     */
    public function generarCodigoPedido(): string
    {
        $year = Carbon::now('America/Lima')->year;
        $prefix = "PED-{$year}-";

        $ultimoPedido = Pedido::where('codigo', 'LIKE', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if (! $ultimoPedido) {
            $numero = 1;
        } else {
            $partes = explode('-', $ultimoPedido->codigo);
            $numero = isset($partes[2]) ? ((int) $partes[2] + 1) : 1;
        }

        return sprintf("%s%04d", $prefix, $numero);
    }

    /**
     * Registra un pedido, sus detalles, descuento de stock e historial en UNA sola transacción.
     */
    public function crearPedido(array $data, int $userId): Pedido
    {
        return DB::transaction(function () use ($data, $userId) {
            // 1. Validar disponibilidad de stock para todas las líneas con producto
            foreach ($data['detalles'] as $linea) {
                if (! empty($linea['producto_id'])) {
                    $producto = Producto::where('id', $linea['producto_id'])->lockForUpdate()->firstOrFail();
                    $cant = (float) $linea['cantidad'];
                    if ((float) $producto->stock_actual < $cant) {
                        $req = number_format($cant, 2);
                        $disp = number_format((float) $producto->stock_actual, 2);
                        throw new InvalidArgumentException("Stock insuficiente para el producto '{$producto->nombre}'. Requerido: {$req}, Disponible: {$disp}.");
                    }
                }
            }

            // 2. Crear pedido
            $codigo = $this->generarCodigoPedido();
            $pedido = Pedido::create([
                'codigo' => $codigo,
                'tipo' => $data['tipo'],
                'cliente_nombre' => trim($data['cliente_nombre']),
                'cliente_telefono' => $data['cliente_telefono'] ?? null,
                'cliente_direccion' => $data['cliente_direccion'] ?? null,
                'estado' => 'recibido',
                'fecha_pedido' => Carbon::now('America/Lima'),
                'fecha_entrega' => ! empty($data['fecha_entrega']) ? $data['fecha_entrega'] : null,
                'observaciones' => $data['observaciones'] ?? null,
                'user_id' => $userId,
            ]);

            // 3. Crear detalles y descontar inventario
            foreach ($data['detalles'] as $linea) {
                $productoId = ! empty($linea['producto_id']) ? (int) $linea['producto_id'] : null;
                DetallePedido::create([
                    'pedido_id' => $pedido->id,
                    'producto_id' => $productoId,
                    'descripcion' => trim($linea['descripcion']),
                    'cantidad' => (float) $linea['cantidad'],
                    'precio_unitario' => isset($linea['precio_unitario']) && $linea['precio_unitario'] !== '' ? (float) $linea['precio_unitario'] : null,
                ]);

                if ($productoId) {
                    $this->inventarioService->registrarSalida(
                        $productoId,
                        (float) $linea['cantidad'],
                        "Pedido {$codigo}",
                        $userId,
                        $pedido->id
                    );
                }
            }

            // 4. Historial inicial
            HistorialEstadoPedido::create([
                'pedido_id' => $pedido->id,
                'estado_anterior' => null,
                'estado_nuevo' => 'recibido',
                'user_id' => $userId,
                'fecha_cambio' => Carbon::now('America/Lima'),
                'observacion' => 'Registro inicial del pedido',
            ]);

            return $pedido;
        });
    }

    /**
     * Avanza el estado del pedido en la secuencia de flujo.
     */
    public function cambiarEstado(Pedido $pedido, string $nuevoEstado, int $userId, ?string $observacion = null): void
    {
        DB::transaction(function () use ($pedido, $nuevoEstado, $userId, $observacion) {
            $anterior = $pedido->estado;
            $pedido->estado = $nuevoEstado;
            $pedido->save();

            HistorialEstadoPedido::create([
                'pedido_id' => $pedido->id,
                'estado_anterior' => $anterior,
                'estado_nuevo' => $nuevoEstado,
                'user_id' => $userId,
                'fecha_cambio' => Carbon::now('America/Lima'),
                'observacion' => $observacion ? trim($observacion) : null,
            ]);
        });
    }

    /**
     * Actualiza datos del cliente y cantidades de los ítems del pedido, ajustando el stock.
     */
    public function actualizarPedido(Pedido $pedido, array $data, int $userId): void
    {
        DB::transaction(function () use ($pedido, $data, $userId) {
            $pedido->update([
                'cliente_nombre' => trim($data['cliente_nombre']),
                'cliente_telefono' => $data['cliente_telefono'] ?? null,
                'cliente_direccion' => $data['cliente_direccion'] ?? null,
                'fecha_entrega' => ! empty($data['fecha_entrega']) ? $data['fecha_entrega'] : null,
                'observaciones' => $data['observaciones'] ?? null,
            ]);

            if (! empty($data['detalles'])) {
                foreach ($data['detalles'] as $itemData) {
                    $detalle = DetallePedido::where('pedido_id', $pedido->id)->where('id', $itemData['id'])->first();
                    if (! $detalle) {
                        continue;
                    }

                    $nuevaCant = (float) $itemData['cantidad'];
                    $cantAnterior = (float) $detalle->cantidad;
                    $precioUnit = isset($itemData['precio_unitario']) && $itemData['precio_unitario'] !== '' ? (float) $itemData['precio_unitario'] : null;

                    if ($detalle->producto_id && abs($nuevaCant - $cantAnterior) > 0.0001) {
                        $diferencia = $nuevaCant - $cantAnterior;
                        if ($diferencia > 0) {
                            $this->inventarioService->registrarSalida(
                                $detalle->producto_id,
                                $diferencia,
                                "Ajuste por edición de Pedido {$pedido->codigo}",
                                $userId,
                                $pedido->id
                            );
                        } else {
                            $this->inventarioService->registrarEntrada(
                                $detalle->producto_id,
                                abs($diferencia),
                                "Devolución por edición de Pedido {$pedido->codigo}",
                                $userId,
                                $pedido->id
                            );
                        }
                    }

                    $detalle->update([
                        'cantidad' => $nuevaCant,
                        'precio_unitario' => $precioUnit,
                    ]);
                }
            }
        });
    }
}

