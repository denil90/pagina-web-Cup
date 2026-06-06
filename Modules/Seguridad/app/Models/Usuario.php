<?php

namespace Modules\Seguridad\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class Usuario extends Authenticatable
{
    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false;

    protected $fillable = [
        'nombre',
        'apellidos',
        'ci',
        'contrasena',
        'fechanac',
        'sexo',
        'direccion',
        'telefono',
        'rol',
        'correo',
        'fecha',
    ];

    protected $hidden = ['contrasena'];

    // Laravel usa 'password' por defecto para auth; redirigimos a 'contrasena'
    public function getAuthPassword(): string
    {
        return $this->contrasena;
    }

    /**
     * Hashing automático al asignar la contraseña.
     * Evita almacenar texto plano en la BD.
     */
    public function setContrasenaAttribute(string $value): void
    {
        $this->attributes['contrasena'] = Hash::make($value);
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombre} {$this->apellidos}";
    }

    public function administrador()
    {
        return $this->hasOne(Administrador::class, 'id_admin', 'id_usuario');
    }

    public function docente()
    {
        $target = class_exists('Modules\Facultad\Models\Docente') 
            ? 'Modules\Facultad\Models\Docente' 
            : 'App\Models\Docente';
        return $this->hasOne($target, 'id_docente', 'id_usuario');
    }

    public function postulante()
    {
        $target = class_exists('Modules\Admision\Models\Postulante') 
            ? 'Modules\Admision\Models\Postulante' 
            : 'App\Models\Postulante';
        return $this->hasOne($target, 'id_postulante', 'id_usuario');
    }

    public function esAdministrador(): bool
    {
        return $this->rol === 'administrador';
    }

    public function esDocente(): bool
    {
        return $this->rol === 'docente';
    }

    public function esPostulante(): bool
    {
        return $this->rol === 'postulante';
    }
}
