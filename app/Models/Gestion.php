<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gestion extends Model
{
    protected $table = 'gestion';
    protected $primaryKey = 'id_gestion';
    public $timestamps = false;

    protected $fillable = [
        'semestre',
        'anio',
    ];

    public function postulantes()
    {
        return $this->hasMany(Postulante::class, 'id_gestion', 'id_gestion');
    }

    public function getNombreCompletoAttribute(): string
    {
        return "Gestión {$this->semestre} - {$this->anio}";
    }
}
