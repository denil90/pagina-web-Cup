<?php

namespace Modules\Facultad\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistroDocentePublicoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Registro público
    }

    public function rules(): array
    {
        return [
            'nombre'              => 'required|string|max:100',
            'apellidos'           => 'required|string|max:100',
            'ci'                  => 'required|string|max:20|unique:usuario,ci',
            'fechanac'            => 'required|date|before:today',
            'sexo'                => 'required|in:M,F',
            'direccion'           => 'required|string|max:255',
            'telefono'            => 'nullable|string|max:20',
            'correo'              => 'required|email|max:150|unique:usuario,correo',
            'contrasena'          => 'required|string|min:6|confirmed',
        ];
    }

    public function messages(): array
    {
        return [
            'ci.unique'       => 'Este carnet de identidad ya está registrado.',
            'correo.unique'   => 'Este correo electrónico ya está registrado.',
            'contrasena.confirmed' => 'Las contraseñas no coinciden.',
            'contrasena.min'  => 'La contraseña debe tener al menos 6 caracteres.',
            'fechanac.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
        ];
    }
}
