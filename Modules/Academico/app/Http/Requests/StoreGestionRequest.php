<?php

namespace Modules\Academico\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'semestre' => 'required|string|max:20',
            'anio'     => 'required|integer|min:2020|max:2050',
        ];
    }
}
