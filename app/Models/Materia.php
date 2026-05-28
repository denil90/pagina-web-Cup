<?php

namespace App\Models;

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

    public function notas()
    {
        return $this->hasMany(Nota::class, 'id_materia', 'id_materia');
    }

    public function docenteGrupos()
    {
        return $this->hasMany(DocenteGrupo::class, 'id_materia', 'id_materia');
    }
}
