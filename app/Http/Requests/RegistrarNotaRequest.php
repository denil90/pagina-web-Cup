<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegistrarNotaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'id_postulante' => 'required|exists:postulante,id_postulante',
            'id_materia' => 'required|exists:materia,id_materia',
            'examen1' => 'nullable|numeric|min:0|max:100',
            'examen2' => 'nullable|numeric|min:0|max:100',
            'examen3' => 'nullable|numeric|min:0|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'examen1.max' => 'La nota del examen 1 no puede superar 100 puntos.',
            'examen2.max' => 'La nota del examen 2 no puede superar 100 puntos.',
            'examen3.max' => 'La nota del examen final no puede superar 100 puntos.',
        ];
    }
}
