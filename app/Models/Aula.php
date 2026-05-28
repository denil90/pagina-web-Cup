<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Aula extends Model
{
    protected $table = 'aula';
    protected $primaryKey = 'id_aula';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'edificio',
        'capacidad',
    ];

    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'id_aula', 'id_aula');
    }

    public function getDescripcionAttribute(): string
    {
        return "{$this->nombre} - Edificio {$this->edificio} (Cap: {$this->capacidad})";
    }
}
