<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReporteGenerado extends Model
{
    use HasFactory;

    protected $table = 'reportes_generados';

    protected $fillable = [
        'tipo',
        'filtros',
        'formato',
        'hora_inicio',
        'hora_fin',
        'duracion_segundos',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'filtros' => 'array',
            'hora_inicio' => 'datetime',
            'hora_fin' => 'datetime',
            'duracion_segundos' => 'decimal:2',
        ];
    }

    /**
     * Usuario que generó el reporte.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
