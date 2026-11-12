<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && in_array($this->user()->rol, ['administrador', 'colaborador'], true);
    }

    public function rules(): array
    {
        $productoId = $this->route('producto')?->id;
        $isCreate = $this->isMethod('POST');

        $rules = [
            'codigo' => ['required', 'string', 'max:30', Rule::unique('productos', 'codigo')->ignore($productoId)],
            'nombre' => ['required', 'string', 'max:150'],
            'categoria_id' => ['required', 'exists:categorias,id'],
            'unidad_medida' => ['required', 'string', 'max:20'],
            'stock_minimo' => ['required', 'numeric', 'min:0'],
            'precio_referencial' => ['nullable', 'numeric', 'min:0'],
            'activo' => ['nullable', 'boolean'],
        ];

        if ($isCreate) {
            $rules['stock_inicial'] = ['nullable', 'numeric', 'min:0'];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'codigo.required' => 'El código del producto es obligatorio.',
            'codigo.unique' => 'El código de producto ya está en uso.',
            'nombre.required' => 'El nombre del producto es obligatorio.',
            'categoria_id.required' => 'Debe seleccionar una categoría.',
            'categoria_id.exists' => 'La categoría seleccionada no es válida.',
            'unidad_medida.required' => 'La unidad de medida es obligatoria.',
            'stock_minimo.required' => 'El stock mínimo es obligatorio.',
            'stock_minimo.min' => 'El stock mínimo no puede ser negativo.',
            'stock_inicial.min' => 'El stock inicial no puede ser negativo.',
            'precio_referencial.min' => 'El precio referencial no puede ser negativo.',
        ];
    }
}
