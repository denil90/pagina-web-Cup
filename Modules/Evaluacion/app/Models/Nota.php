<?php

namespace Modules\Evaluacion\Models;

use Illuminate\Database\Eloquent\Model;

class Nota extends Model
{
    protected $table = 'notas';
    public $incrementing = false;
    public $timestamps = false;

    // Clave compuesta: el trigger de PostgreSQL calcula promedio y estado automáticamente
    protected $primaryKey = ['id_postulante', 'id_materia'];

    protected $fillable = [
        'id_postulante',
        'id_materia',
        'examen1',
        'examen2',
        'examen3',
        'promedio',
        'estado',
    ];

    protected $casts = [
        'examen1' => 'float',
        'examen2' => 'float',
        'examen3' => 'float',
        'promedio' => 'float',
    ];

    /**
     * Eloquent no soporta claves compuestas de forma nativa.
     */
    public function getKeyName()
    {
        return ['id_postulante', 'id_materia'];
    }

    protected function setKeysForSaveQuery($query)
    {
        $query->where('id_postulante', '=', $this->getAttribute('id_postulante'))
              ->where('id_materia', '=', $this->getAttribute('id_materia'));
        return $query;
    }

    public function postulante()
    {
        $target = class_exists('Modules\Admision\Models\Postulante')
            ? 'Modules\Admision\Models\Postulante'
            : 'App\Models\Postulante';
        return $this->belongsTo($target, 'id_postulante', 'id_postulante');
    }

    public function materia()
    {
        $target = class_exists('Modules\Academico\Models\Materia')
            ? 'Modules\Academico\Models\Materia'
            : 'App\Models\Materia';
        return $this->belongsTo($target, 'id_materia', 'id_materia');
    }

    public function estaAprobado(): bool
    {
        return $this->estado === 'APROBADO';
    }
}
