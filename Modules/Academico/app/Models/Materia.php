<?php

namespace Modules\Academico\Models;

use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    protected $table = 'materia';
    protected $primaryKey = 'id_materia';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'porcentaje_examen1',
        'porcentaje_examen2',
        'porcentaje_examen3',
    ];

    protected $casts = [
        'porcentaje_examen1' => 'float',
        'porcentaje_examen2' => 'float',
        'porcentaje_examen3' => 'float',
    ];
}
