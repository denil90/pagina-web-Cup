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
        $latestGestion = class_exists('Modules\Academico\Models\Gestion')
            ? \Modules\Academico\Models\Gestion::orderByDesc('anio')->orderByDesc('semestre')->first()
            : \App\Models\Gestion::orderByDesc('anio')->orderByDesc('semestre')->first();
        $currentGestionId = $latestGestion ? $latestGestion->id_gestion : null;

        return $this->postulantes()
            ->where('id_gestion', $currentGestionId)
            ->count();
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

    public function getHorarioRangoAttribute(): string
    {
        if ($this->horario) {
            return $this->horario->rango;
        }

        $turnoNombre = $this->turno?->nombre;
        if ($turnoNombre === 'Mañana') {
            return '07:00 - 11:00';
        } elseif ($turnoNombre === 'Tarde') {
            return '14:00 - 18:00';
        } elseif ($turnoNombre === 'Noche') {
            return '19:00 - 23:00';
        }

        return '—';
    }
}
