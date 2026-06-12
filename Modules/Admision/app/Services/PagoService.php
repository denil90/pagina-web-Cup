<?php

namespace Modules\Admision\Services;

use Modules\Admision\Models\Pago;
use Modules\Admision\Models\Postulante;

class PagoService
{
    private const MONTO_INSCRIPCION = 700.00;
    private const MONEDA = 'BOB';

    public function obtenerMontoInscripcion(): float
    {
        return self::MONTO_INSCRIPCION;
    }

    public function obtenerMoneda(): string
    {
        return self::MONEDA;
    }

    /**
     * Crea un registro de pago pendiente para el postulante.
     */
    public function crearPagoPendiente(int $postulanteId): Pago
    {
        $pagoExistente = Pago::where('id_postulante', $postulanteId)->first();

        if ($pagoExistente && $pagoExistente->estaCompletado()) {
            throw new \RuntimeException('El postulante ya tiene un pago completado.');
        }

        if ($pagoExistente) {
            return $pagoExistente;
        }

        return Pago::create([
            'id_postulante' => $postulanteId,
            'monto' => self::MONTO_INSCRIPCION,
            'moneda' => self::MONEDA,
            'estado' => 'PENDIENTE',
        ]);
    }

    /**
     * Confirma el pago tras recibir la aprobación de PayPal.
     */
    public function confirmarPago(int $postulanteId, string $paypalOrderId): Pago
    {
        $pago = Pago::where('id_postulante', $postulanteId)
            ->where('estado', 'PENDIENTE')
            ->firstOrFail();

        $pago->update([
            'paypal_order_id' => $paypalOrderId,
            'estado' => 'COMPLETADO',
            'fecha_pago' => now(),
        ]);

        // Intentar asignación automática de grupo
        $postulante = Postulante::with('pago')->find($postulanteId);
        if ($postulante) {
            $grupoService = app(\Modules\Planificacion\Services\GrupoService::class);
            $grupoService->intentarAsignacionAutomatica($postulante);
        }

        return $pago->fresh();
    }

    public function obtenerEstadoPago(int $postulanteId): ?Pago
    {
        return Pago::where('id_postulante', $postulanteId)->first();
    }

    public function estaEnModoTest(): bool
    {
        $clientId = config('services.paypal.client_id');
        return empty($clientId) || $clientId === 'PAYPAL_SANDBOX_CLIENT_ID';
    }

    public function obtenerClientId(): string
    {
        return config('services.paypal.client_id', 'PAYPAL_SANDBOX_CLIENT_ID');
    }
}
