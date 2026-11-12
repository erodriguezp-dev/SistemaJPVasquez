<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CambioEstadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        if (! $user) {
            return false;
        }

        $esRetroceso = $this->boolean('es_retroceso');
        if ($esRetroceso) {
            return $user->rol === 'administrador';
        }

        return in_array($user->rol, ['administrador', 'gerente_proyecto', 'colaborador']);
    }

    public function rules(): array
    {
        $esRetroceso = $this->boolean('es_retroceso');

        return [
            'nuevo_estado' => ['required', 'in:recibido,en_proceso,terminado,entregado,confirmado'],
            'es_retroceso' => ['required', 'boolean'],
            'observacion' => [$esRetroceso ? 'required' : 'nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'nuevo_estado.required' => 'Debe especificar el nuevo estado del pedido.',
            'observacion.required' => 'La observación es obligatoria al retroceder el estado de un pedido.',
        ];
    }
}
