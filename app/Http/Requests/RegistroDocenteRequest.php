<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistroDocenteRequest extends FormRequest
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
            'contrasena' => 'required|string|min:6',
            'fechanac' => 'required|date|before:today',
            'sexo' => 'required|in:M,F',
            'direccion' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'required|email|max:150|unique:usuario,correo',
            'titulo_profesional' => 'required|string|max:150',
            'maestria' => 'nullable|string|max:150',
            'diplomado' => 'nullable|string|max:150',
        ];
    }

    public function messages(): array
    {
        return [
            'ci.unique' => 'Este carnet de identidad ya está registrado.',
            'correo.unique' => 'Este correo electrónico ya está en uso.',
            'titulo_profesional.required' => 'El título profesional es obligatorio para docentes.',
        ];
    }
}
