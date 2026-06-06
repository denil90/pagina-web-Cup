<?php

namespace Modules\Academico\Models;

use Illuminate\Database\Eloquent\Model;

class Carrera extends Model
{
    protected $table = 'carrera';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'descripcion',
        'cupo_maximo',
    ];

    /**
     * Cuenta los admitidos consultando directamente la tabla admision_final.
     */
    public function getAdmitidosCountAttribute(): int
    {
        return \Illuminate\Support\Facades\DB::table('admision_final')
            ->where('id_carrera_admitida', $this->id)
            ->count();
    }

    /**
     * Los cupos disponibles se calculan consultando admision_final.
     * Pero esa tabla pertenece al módulo Evaluacion, así que esta relación
     * se usa SOLO dentro del módulo Academico para el listado.
     * Para consultas cross-módulo, usar CareerQueryInterface.
     */
    public function cuposDisponibles(): int
    {
        return $this->cupo_maximo - $this->admitidos_count;
    }
}
