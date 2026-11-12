<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->rol === 'administrador';
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'usuario' => ['required', 'string', 'max:50', 'unique:users,usuario', 'regex:/^[a-zA-Z0-9_.-]+$/'],
            'email' => ['nullable', 'email', 'max:120', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'rol' => ['required', 'in:administrador,contador,colaborador,gerente_proyecto'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre completo es obligatorio.',
            'name.max' => 'El nombre no puede exceder los 100 caracteres.',
            'usuario.required' => 'El usuario es obligatorio.',
            'usuario.unique' => 'El nombre de usuario ya está registrado.',
            'usuario.regex' => 'El usuario solo puede contener letras, números, puntos, guiones y guiones bajos.',
            'email.email' => 'Ingrese una dirección de correo electrónico válida.',
            'email.unique' => 'El correo electrónico ya está registrado por otro usuario.',
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'rol.required' => 'Debe seleccionar un rol para el usuario.',
            'rol.in' => 'El rol seleccionado no es válido.',
        ];
    }
}
