<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PedidoUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->rol, ['administrador', 'gerente_proyecto']);
    }

    public function rules(): array
    {
        return [
            'cliente_nombre' => ['required', 'string', 'max:150'],
            'cliente_telefono' => ['nullable', 'string', 'regex:/^[0-9]{9}$/'],
            'cliente_direccion' => ['nullable', 'string', 'max:255'],
            'fecha_entrega' => ['nullable', 'date'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
            'detalles' => ['nullable', 'array'],
            'detalles.*.id' => ['required', 'exists:detalle_pedido,id'],
            'detalles.*.cantidad' => ['required', 'numeric', 'gt:0'],
            'detalles.*.precio_unitario' => ['nullable', 'numeric', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'cliente_nombre.required' => 'El nombre del cliente es obligatorio.',
            'cliente_telefono.regex' => 'El teléfono de contacto debe tener exactamente 9 dígitos numéricos.',
            'detalles.*.cantidad.required' => 'La cantidad es obligatoria.',
            'detalles.*.cantidad.gt' => 'La cantidad debe ser mayor a 0.',
        ];
    }
}

