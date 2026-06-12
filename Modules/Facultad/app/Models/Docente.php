<?php

namespace Modules\Facultad\Models;

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
        'archivo_titulo',
        'archivo_maestria',
        'archivo_diplomado',
    ];

    public function usuario()
    {
        $target = class_exists('Modules\Seguridad\Models\Usuario')
            ? 'Modules\Seguridad\Models\Usuario'
            : 'App\Models\Usuario';
        return $this->belongsTo($target, 'id_docente', 'id_usuario');
    }

    public function gruposAsignados()
    {
        return $this->hasMany(DocenteGrupo::class, 'id_docente', 'id_docente');
    }

    public function grupos()
    {
        $target = class_exists('Modules\Planificacion\Models\Grupo')
            ? 'Modules\Planificacion\Models\Grupo'
            : 'App\Models\Grupo';
        return $this->belongsToMany($target, 'docente_grupo', 'id_docente', 'id_grupo')
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
