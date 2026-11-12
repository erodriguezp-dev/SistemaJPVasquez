<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MovimientoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && in_array($this->user()->rol, ['administrador', 'colaborador'], true);
    }

    public function rules(): array
    {
        return [
            'producto_id' => ['required', 'exists:productos,id'],
            'tipo' => ['required', 'in:entrada,salida'],
            'cantidad' => ['required', 'numeric', 'gt:0'],
            'motivo' => ['required', 'string', 'max:255'],
            'fecha_movimiento' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'producto_id.required' => 'Debe seleccionar un producto.',
            'producto_id.exists' => 'El producto seleccionado no existe.',
            'tipo.required' => 'Debe seleccionar el tipo de movimiento.',
            'tipo.in' => 'El tipo de movimiento debe ser entrada o salida.',
            'cantidad.required' => 'La cantidad es obligatoria.',
            'cantidad.gt' => 'La cantidad debe ser mayor a cero.',
            'motivo.required' => 'El motivo del movimiento es obligatorio.',
            'motivo.max' => 'El motivo no puede exceder los 255 caracteres.',
        ];
    }
}
