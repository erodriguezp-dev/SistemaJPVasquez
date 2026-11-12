<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ReporteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->rol, ['administrador', 'gerente_proyecto', 'contador']);
    }

    public function rules(): array
    {
        return [
            'tipo' => ['required', 'in:inventario,pedidos'],
            'formato' => ['required', 'in:pantalla,pdf,excel'],
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'categoria_id' => ['nullable', 'exists:categorias,id'],
            'producto_id' => ['nullable', 'exists:productos,id'],
            'estado_stock' => ['nullable', 'in:normal,bajo,agotado'],
            'estado_pedido' => ['nullable', 'in:recibido,en_proceso,terminado,entregado,confirmado'],
            'tipo_pedido' => ['nullable', 'in:mueble,ferreteria,reparacion'],
            'incluir_movimientos' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'tipo.required' => 'Debe seleccionar el tipo de reporte a generar.',
            'formato.required' => 'Debe seleccionar el formato de salida.',
            'fecha_fin.after_or_equal' => 'La fecha final no puede ser anterior a la fecha inicial.',
        ];
    }
}
