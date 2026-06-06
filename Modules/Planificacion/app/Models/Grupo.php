<?php

namespace Modules\Planificacion\Models;

use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    protected $table = 'grupo';
    protected $primaryKey = 'id_grupo';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'capacidad_maxima',
        'id_horario',
        'id_aula',
        'id_turno',
    ];

    public function horario()
    {
        return $this->belongsTo(Horario::class, 'id_horario', 'id_horario');
    }

    public function aula()
    {
        return $this->belongsTo(Aula::class, 'id_aula', 'id_aula');
    }

    public function turno()
    {
        return $this->belongsTo(Turno::class, 'id_turno', 'id_turno');
    }

    public function postulantes()
    {
        $target = class_exists('Modules\Admision\Models\Postulante')
            ? 'Modules\Admision\Models\Postulante'
            : 'App\Models\Postulante';
        return $this->hasMany($target, 'id_grupo', 'id_grupo');
    }

    public function docenteGrupos()
    {
        $target = class_exists('Modules\Facultad\Models\DocenteGrupo')
            ? 'Modules\Facultad\Models\DocenteGrupo'
            : 'App\Models\DocenteGrupo';
        return $this->hasMany($target, 'id_grupo', 'id_grupo');
    }

    public function inscritosActuales(): int
    {
        return $this->postulantes()->count();
    }

    public function tieneDisponibilidad(): bool
    {
        return $this->inscritosActuales() < $this->capacidad_maxima;
    }

    public function porcentajeOcupacion(): float
    {
        if ($this->capacidad_maxima === 0) {
            return 0;
        }
        return round(($this->inscritosActuales() / $this->capacidad_maxima) * 100, 1);
    }
}
