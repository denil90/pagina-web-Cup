<?php

namespace App\Http\Controllers\Postulante;

use App\Http\Controllers\Controller;
use App\Services\PagoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PagoController extends Controller
{
    public function __construct(
        private PagoService $pagoService
    ) {}

    public function index()
    {
        $postulante = Auth::user()->postulante;
        $pago = $this->pagoService->obtenerEstadoPago($postulante->id_postulante);
        $monto = $this->pagoService->obtenerMontoInscripcion();
        $moneda = $this->pagoService->obtenerMoneda();
        $modoTest = $this->pagoService->estaEnModoTest();
        $clientId = $this->pagoService->obtenerClientId();

        return view('postulante.pago', compact('postulante', 'pago', 'monto', 'moneda', 'modoTest', 'clientId'));
    }

    public function crearPago()
    {
        try {
            $postulante = Auth::user()->postulante;
            $this->pagoService->crearPagoPendiente($postulante->id_postulante);
            return back()->with('success', 'Pago pendiente creado. Proceda con PayPal.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Confirma el pago después de la aprobación de PayPal (o simulación en test).
     */
    public function confirmar(Request $request)
    {
        $request->validate([
            'paypal_order_id' => 'required|string',
        ]);

        try {
            $postulante = Auth::user()->postulante;
            $this->pagoService->confirmarPago($postulante->id_postulante, $request->paypal_order_id);

            return response()->json(['success' => true, 'message' => 'Pago confirmado exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    /**
     * Simula la aprobación del pago en modo test (sin PayPal real).
     */
    public function simularPago()
    {
        if (!$this->pagoService->estaEnModoTest()) {
            return back()->with('error', 'La simulación solo está disponible en modo test.');
        }

        try {
            $postulante = Auth::user()->postulante;
            $this->pagoService->crearPagoPendiente($postulante->id_postulante);
            $this->pagoService->confirmarPago($postulante->id_postulante, 'TEST-' . uniqid());

            return back()->with('success', 'Pago simulado exitosamente (modo test).');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
