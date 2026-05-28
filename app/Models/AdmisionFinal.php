<?php

namespace App\Models;

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
        return $this->belongsTo(Postulante::class, 'id_postulante', 'id_postulante');
    }

    public function carrera()
    {
        return $this->belongsTo(Carrera::class, 'id_carrera_admitida', 'id');
    }

    public function fueAdmitidoEnPrimeraOpcion(): bool
    {
        return $this->opcion_ingreso === 'PRIMERA OPCION';
    }
}
