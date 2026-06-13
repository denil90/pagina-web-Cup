<?php

namespace Modules\Academico\Models;

use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    protected $table = 'materia';
    protected $primaryKey = 'id_materia';
    public $timestamps = false;
//2- Existe trazabilidad de la arquitectura entre el analisis, analisis paquete, diseño, implementación (Documento, codigo)
    protected $fillable = [
        'nombre',
        'porcentaje_examen1',
        'porcentaje_examen2',
        'porcentaje_examen3',
    ];
//cast para convertir los porcentajes a float
    protected $casts = [
        'porcentaje_examen1' => 'float',
        
        'porcentaje_examen2' => 'float',
        'porcentaje_examen3' => 'float',
    ];
}
