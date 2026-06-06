<?php

namespace Modules\Academico\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCarreraRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombre'      => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'cupo_maximo' => 'required|integer|min:1',
        ];
    }
}
