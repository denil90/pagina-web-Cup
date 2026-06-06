<?php

namespace Modules\Facultad\Models;

use Illuminate\Database\Eloquent\Model;

class DocenteGrupo extends Model
{
    protected $table = 'docente_grupo';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'id_docente',
        'id_grupo',
        'id_materia',
    ];

    public function docente()
    {
        return $this->belongsTo(Docente::class, 'id_docente', 'id_docente');
    }

    public function grupo()
    {
        $target = class_exists('Modules\Planificacion\Models\Grupo')
            ? 'Modules\Planificacion\Models\Grupo'
            : 'App\Models\Grupo';
        return $this->belongsTo($target, 'id_grupo', 'id_grupo');
    }

    public function materia()
    {
        $target = class_exists('Modules\Academico\Models\Materia')
            ? 'Modules\Academico\Models\Materia'
            : 'App\Models\Materia';
        return $this->belongsTo($target, 'id_materia', 'id_materia');
    }
}
