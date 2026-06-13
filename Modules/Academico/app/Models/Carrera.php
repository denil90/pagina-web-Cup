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
     * Cuenta los admitidos para una gestión específica.
     */
    public function admitidosCount(?int $id_gestion = null): int
    {
        $query = \Illuminate\Support\Facades\DB::table('admision_final')
            ->where('id_carrera_admitida', $this->id);

        if ($id_gestion) {
            $query->join('postulante', 'postulante.id_postulante', '=', 'admision_final.id_postulante')
                ->where('postulante.id_gestion', $id_gestion);
        }

        return $query->count();
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
