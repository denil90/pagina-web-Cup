<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Turno extends Model
{
    protected $table = 'turno';
    protected $primaryKey = 'id_turno';
    public $timestamps = false;

    protected $fillable = ['nombre'];

    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'id_turno', 'id_turno');
    }
}
