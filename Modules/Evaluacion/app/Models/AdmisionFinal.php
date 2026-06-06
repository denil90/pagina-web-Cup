<?php

namespace Modules\Evaluacion\Models;

use Illuminate\Database\Eloquent\Model;

class AdmisionFinal extends Model
{
    protected $table = 'admision_final';
    protected $primaryKey = 'id_postulante';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_postulante',
        'id_carrera_admitida',
        'nota_final_cup',
        'opcion_ingreso',
    ];

    protected $casts = [
        'nota_final_cup' => 'float',
    ];

    public function postulante()
    {
        $target = class_exists('Modules\Admision\Models\Postulante')
            ? 'Modules\Admision\Models\Postulante'
            : 'App\Models\Postulante';
        return $this->belongsTo($target, 'id_postulante', 'id_postulante');
    }

    public function carrera()
    {
        $target = class_exists('Modules\Academico\Models\Carrera')
            ? 'Modules\Academico\Models\Carrera'
            : 'App\Models\Carrera';
        return $this->belongsTo($target, 'id_carrera_admitida', 'id');
    }

    public function fueAdmitidoEnPrimeraOpcion(): bool
    {
        return $this->opcion_ingreso === 'PRIMERA OPCION';
    }
}
