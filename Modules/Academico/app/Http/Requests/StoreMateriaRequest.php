<?php

namespace Modules\Academico\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMateriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'             => 'required|string|max:150',
            'porcentaje_examen1' => 'required|numeric|min:0|max:100',
            'porcentaje_examen2' => 'required|numeric|min:0|max:100',
            'porcentaje_examen3' => 'required|numeric|min:0|max:100',
        ];
    }
}
