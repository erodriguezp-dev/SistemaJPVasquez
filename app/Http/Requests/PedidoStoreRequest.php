<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PedidoStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return in_array($this->user()?->rol, ['administrador', 'gerente_proyecto', 'colaborador']);
    }

    public function rules(): array
    {
        return [
            'tipo' => ['required', 'in:mueble,ferreteria,reparacion'],
            'cliente_nombre' => ['required', 'string', 'max:150'],
            'cliente_telefono' => ['nullable', 'string', 'regex:/^[0-9]{9}$/'],
            'cliente_direccion' => ['nullable', 'string', 'max:255'],
            'fecha_entrega' => ['nullable', 'date'],
            'observaciones' => ['nullable', 'string', 'max:1000'],
            'detalles' => ['required', 'array', 'min:1'],
            'detalles.*.descripcion' => ['required', 'string', 'max:255'],
            'detalles.*.cantidad' => ['required', 'numeric', 'gt:0'],
            'detalles.*.precio_unitario' => ['nullable', 'numeric', 'min:0'],
            'detalles.*.producto_id' => ['nullable', 'exists:productos,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'tipo.required' => 'El tipo de pedido es obligatorio.',
            'tipo.in' => 'El tipo de pedido seleccionado no es válido.',
            'cliente_nombre.required' => 'El nombre del cliente es obligatorio.',
            'cliente_telefono.regex' => 'El teléfono de contacto debe tener exactamente 9 dígitos numéricos.',
            'detalles.required' => 'Debe agregar al menos una línea de detalle al pedido.',
            'detalles.min' => 'Debe agregar al menos una línea de detalle al pedido.',
            'detalles.*.descripcion.required' => 'La descripción del ítem es obligatoria.',
            'detalles.*.cantidad.required' => 'La cantidad es obligatoria.',
            'detalles.*.cantidad.gt' => 'La cantidad debe ser mayor a 0.',
            'detalles.*.producto_id.exists' => 'El producto seleccionado no existe en el catálogo.',
        ];
    }
}

