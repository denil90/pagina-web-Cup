<?php

namespace Modules\Admision\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUserInheritance;

class Postulante extends Model
{
    use HasUserInheritance;

    protected $table = 'postulante';
    protected $primaryKey = 'id_postulante';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_postulante',
        'colegio_procedencia',
        'ciudad',
        'titulo_bachiller',
        'libreta_de_ultimo_anio',
        'id_carrera_primera',
        'id_carrera_segunda',
        'id_grupo',
        'id_gestion',
        'archivo_titulo_bachiller',
        'archivo_libreta',
    ];

    protected $casts = [
        'titulo_bachiller' => 'boolean',
        'libreta_de_ultimo_anio' => 'boolean',
    ];

    public function usuario()
    {
        $target = class_exists('Modules\Seguridad\Models\Usuario')
            ? 'Modules\Seguridad\Models\Usuario'
            : 'App\Models\Usuario';
        return $this->belongsTo($target, 'id_postulante', 'id_usuario');
    }

    public function carreraPrimera()
    {
        $target = class_exists('Modules\Academico\Models\Carrera')
            ? 'Modules\Academico\Models\Carrera'
            : 'App\Models\Carrera';
        return $this->belongsTo($target, 'id_carrera_primera', 'id');
    }

    public function carreraSegunda()
    {
        $target = class_exists('Modules\Academico\Models\Carrera')
            ? 'Modules\Academico\Models\Carrera'
            : 'App\Models\Carrera';
        return $this->belongsTo($target, 'id_carrera_segunda', 'id');
    }

    public function grupo()
    {
        $target = class_exists('Modules\Planificacion\Models\Grupo')
            ? 'Modules\Planificacion\Models\Grupo'
            : 'App\Models\Grupo';
        return $this->belongsTo($target, 'id_grupo', 'id_grupo');
    }

    public function gestion()
    {
        $target = class_exists('Modules\Academico\Models\Gestion')
            ? 'Modules\Academico\Models\Gestion'
            : 'App\Models\Gestion';
        return $this->belongsTo($target, 'id_gestion', 'id_gestion');
    }

    public function notas()
    {
        $target = class_exists('Modules\Evaluacion\Models\Nota')
            ? 'Modules\Evaluacion\Models\Nota'
            : 'App\Models\Nota';
        return $this->hasMany($target, 'id_postulante', 'id_postulante');
    }

    public function admisionFinal()
    {
        $target = class_exists('Modules\Evaluacion\Models\AdmisionFinal')
            ? 'Modules\Evaluacion\Models\AdmisionFinal'
            : 'App\Models\AdmisionFinal';
        return $this->hasOne($target, 'id_postulante', 'id_postulante');
    }

    public function pago()
    {
        return $this->hasOne(Pago::class, 'id_postulante', 'id_postulante');
    }

    public function cumpleRequisitos(): bool
    {
        return $this->titulo_bachiller && $this->libreta_de_ultimo_anio;
    }

    public function tienePagoConfirmado(): bool
    {
        return $this->pago && $this->pago->estado === 'COMPLETADO';
    }

    /**
     * Verifica si aprobó TODAS las materias (>=60 en cada una).
     */
    public function aproboTodasLasMaterias(): bool
    {
        $notas = $this->notas;
        if ($notas->isEmpty()) {
            return false;
        }

        return $notas->every(fn($nota) => $nota->promedio >= 60);
    }

    public function promedioGeneral(): float
    {
        return round($this->notas->avg('promedio') ?? 0, 2);
    }
}
