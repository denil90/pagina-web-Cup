<?php

namespace App\Models;

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

    public function postulantesOpcionPrimera()
    {
        return $this->hasMany(Postulante::class, 'id_carrera_primera', 'id');
    }

    public function postulantesOpcionSegunda()
    {
        return $this->hasMany(Postulante::class, 'id_carrera_segunda', 'id');
    }

    public function admitidos()
    {
        return $this->hasMany(AdmisionFinal::class, 'id_carrera_admitida', 'id');
    }

    public function cuposDisponibles(): int
    {
        return $this->cupo_maximo - $this->admitidos()->count();
    }
}
