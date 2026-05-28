<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Postulante extends Model
{
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
    ];

    protected $casts = [
        'titulo_bachiller' => 'boolean',
        'libreta_de_ultimo_anio' => 'boolean',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_postulante', 'id_usuario');
    }

    public function carreraPrimera()
    {
        return $this->belongsTo(Carrera::class, 'id_carrera_primera', 'id');
    }

    public function carreraSegunda()
    {
        return $this->belongsTo(Carrera::class, 'id_carrera_segunda', 'id');
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'id_grupo', 'id_grupo');
    }

    public function gestion()
    {
        return $this->belongsTo(Gestion::class, 'id_gestion', 'id_gestion');
    }

    public function notas()
    {
        return $this->hasMany(Nota::class, 'id_postulante', 'id_postulante');
    }

    public function admisionFinal()
    {
        return $this->hasOne(AdmisionFinal::class, 'id_postulante', 'id_postulante');
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
     * Regla de negocio central del CUP.
     */
    public function aproboTodasLasMaterias(): bool
    {
        $notas = $this->notas;
        if ($notas->isEmpty()) {
            return false;
        }

        return $notas->every(fn(Nota $nota) => $nota->promedio >= 60);
    }

    public function promedioGeneral(): float
    {
        return round($this->notas->avg('promedio') ?? 0, 2);
    }
}
