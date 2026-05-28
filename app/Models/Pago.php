<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pago extends Model
{
    protected $table = 'pago';
    protected $primaryKey = 'id_pago';
    public $timestamps = false;

    protected $fillable = [
        'id_postulante',
        'monto',
        'moneda',
        'paypal_order_id',
        'estado',
        'fecha_pago',
    ];

    protected $casts = [
        'monto' => 'float',
        'fecha_pago' => 'datetime',
    ];

    public function postulante()
    {
        return $this->belongsTo(Postulante::class, 'id_postulante', 'id_postulante');
    }

    public function estaCompletado(): bool
    {
        return $this->estado === 'COMPLETADO';
    }

    public function estaPendiente(): bool
    {
        return $this->estado === 'PENDIENTE';
    }
}
