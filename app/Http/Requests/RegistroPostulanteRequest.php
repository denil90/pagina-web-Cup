<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistroPostulanteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'ci' => 'required|string|max:20|unique:usuario,ci',
            'contrasena' => 'required|string|min:6|confirmed',
            'fechanac' => 'required|date|before:today',
            'sexo' => 'required|in:M,F',
            'direccion' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'required|email|max:150|unique:usuario,correo',
            'colegio_procedencia' => 'required|string|max:150',
            'ciudad' => 'required|string|max:100',
            'id_carrera_primera' => 'required|exists:carrera,id',
            'id_carrera_segunda' => 'nullable|exists:carrera,id|different:id_carrera_primera',
        ];
    }

    public function messages(): array
    {
        return [
            'ci.unique' => 'Este carnet de identidad ya está registrado.',
            'correo.unique' => 'Este correo electrónico ya está en uso.',
            'contrasena.confirmed' => 'Las contraseñas no coinciden.',
            'fechanac.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            'id_carrera_segunda.different' => 'La segunda carrera debe ser diferente a la primera.',
        ];
    }
}
