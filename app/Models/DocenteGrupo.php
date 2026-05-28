<?php

namespace App\Models;

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
        return $this->belongsTo(Grupo::class, 'id_grupo', 'id_grupo');
    }

    public function materia()
    {
        return $this->belongsTo(Materia::class, 'id_materia', 'id_materia');
    }
}
