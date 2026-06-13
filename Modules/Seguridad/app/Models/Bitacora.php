<?php

namespace Modules\Seguridad\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class Bitacora extends Model
{
    protected $table = 'bitacora';

    protected $fillable = [
        'id_usuario',
        'accion',
        'modulo',
        'descripcion',
        'ip_address',
    ];

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    /**
     * Registra una acción en la bitácora automáticamente.
     */
    public static function registrar(string $accion, string $modulo, string $descripcion): void
    {
        self::create([
            'id_usuario'  => Auth::id(),
            'accion'      => $accion,
            'modulo'      => $modulo,
            'descripcion' => $descripcion,
            'ip_address'  => Request::ip(),
        ]);
    }
}
