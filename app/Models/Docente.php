<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Docente extends Model
{
    protected $table = 'docente';
    protected $primaryKey = 'id_docente';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_docente',
        'titulo_profesional',
        'maestria',
        'diplomado',
        'estado',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_docente', 'id_usuario');
    }

    public function gruposAsignados()
    {
        return $this->hasMany(DocenteGrupo::class, 'id_docente', 'id_docente');
    }

    public function grupos()
    {
        return $this->belongsToMany(Grupo::class, 'docente_grupo', 'id_docente', 'id_grupo')
                    ->withPivot('id_materia');
    }

    public function cantidadGrupos(): int
    {
        return $this->gruposAsignados()->distinct('id_grupo')->count('id_grupo');
    }

    public function estaActivo(): bool
    {
        return $this->estado === 'ACTIVO';
    }
}
