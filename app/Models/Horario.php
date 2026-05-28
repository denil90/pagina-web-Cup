<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    protected $table = 'horario';
    protected $primaryKey = 'id_horario';
    public $timestamps = false;

    protected $fillable = [
        'dia',
        'hora_inicio',
        'hora_final',
    ];

    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'id_horario', 'id_horario');
    }

    public function getRangoAttribute(): string
    {
        return "{$this->dia} {$this->hora_inicio} - {$this->hora_final}";
    }
}
