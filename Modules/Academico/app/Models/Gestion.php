<?php

namespace Modules\Academico\Models;

use Illuminate\Database\Eloquent\Model;

class Gestion extends Model
{
    protected $table = 'gestion';
    protected $primaryKey = 'id_gestion';
    public $timestamps = false;

    protected $fillable = [
        'semestre',
        'anio',
    ];
//accesor para mostrar el nombre completo de la gestion, se muestra el semestre y el año
    public function getNombreCompletoAttribute(): string
    {
        return "Gestión {$this->semestre} - {$this->anio}";
    }
}
