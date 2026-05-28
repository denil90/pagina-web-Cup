<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Administrador extends Model
{
    protected $table = 'administrador';
    protected $primaryKey = 'id_admin';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = ['id_admin'];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_admin', 'id_usuario');
    }
}
