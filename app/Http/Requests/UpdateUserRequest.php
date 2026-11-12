<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->rol === 'administrador';
    }

    public function rules(): array
    {
        $targetUser = $this->route('usuario'); // Route parameter Model or ID
        $userId = is_object($targetUser) ? $targetUser->id : $targetUser;

        return [
            'name' => ['required', 'string', 'max:100'],
            'usuario' => [
                'required',
                'string',
                'max:50',
                Rule::unique('users', 'usuario')->ignore($userId),
                'regex:/^[a-zA-Z0-9_.-]+$/'
            ],
            'email' => [
                'nullable',
                'email',
                'max:120',
                Rule::unique('users', 'email')->ignore($userId)
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'rol' => ['required', 'in:administrador,contador,colaborador,gerente_proyecto'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre completo es obligatorio.',
            'name.max' => 'El nombre no puede exceder los 100 caracteres.',
            'usuario.required' => 'El usuario es obligatorio.',
            'usuario.unique' => 'El nombre de usuario ya pertenece a otra cuenta.',
            'usuario.regex' => 'El usuario solo puede contener letras, números, puntos, guiones y guiones bajos.',
            'email.email' => 'Ingrese una dirección de correo electrónico válida.',
            'email.unique' => 'El correo electrónico ya está registrado por otro usuario.',
            'password.min' => 'La nueva contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'rol.required' => 'Debe seleccionar un rol para el usuario.',
            'rol.in' => 'El rol seleccionado no es válido.',
        ];
    }
}
