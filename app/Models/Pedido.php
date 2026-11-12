<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pedido extends Model
{
    use HasFactory;

    protected $table = 'pedidos';

    protected $fillable = [
        'codigo',
        'tipo',
        'cliente_nombre',
        'cliente_telefono',
        'cliente_direccion',
        'estado',
        'fecha_pedido',
        'fecha_entrega',
        'observaciones',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'fecha_pedido' => 'datetime',
            'fecha_entrega' => 'date',
        ];
    }

    /**
     * Usuario que registró el pedido.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Detalles o items asociados al pedido.
     */
    public function detalles(): HasMany
    {
        return $this->hasMany(DetallePedido::class, 'pedido_id');
    }

    /**
     * Movimientos de inventario generados por el pedido.
     */
    public function movimientosInventario(): HasMany
    {
        return $this->hasMany(MovimientoInventario::class, 'pedido_id');
    }

    /**
     * Historial de transiciones de estado del pedido.
     */
    public function historialEstados(): HasMany
    {
        return $this->hasMany(HistorialEstadoPedido::class, 'pedido_id');
    }
}
