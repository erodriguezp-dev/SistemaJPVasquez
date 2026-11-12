<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AjusteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && in_array($this->user()->rol, ['administrador', 'colaborador'], true);
    }

    public function rules(): array
    {
        return [
            'producto_id' => ['required', 'exists:productos,id'],
            'stock_fisico' => ['required', 'numeric', 'min:0'],
            'motivo' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'producto_id.required' => 'Debe seleccionar un producto.',
            'producto_id.exists' => 'El producto seleccionado no es válido.',
            'stock_fisico.required' => 'El stock físico contado es obligatorio.',
            'stock_fisico.min' => 'El stock físico no puede ser un valor negativo.',
            'motivo.required' => 'El motivo del ajuste es obligatorio.',
            'motivo.max' => 'El motivo no puede exceder los 255 caracteres.',
        ];
    }
}
