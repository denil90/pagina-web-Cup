<?php

namespace App\Http\Controllers\Postulante;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PostulanteDashboardController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();
        $postulante = $usuario->postulante()->with([
            'carreraPrimera', 'carreraSegunda', 'grupo.horario',
            'grupo.turno', 'gestion', 'notas.materia',
            'pago', 'admisionFinal.carrera'
        ])->first();

        $estadoInscripcion = $this->determinarEstado($postulante);

        return view('postulante.dashboard', compact('usuario', 'postulante', 'estadoInscripcion'));
    }

    private function determinarEstado($postulante): array
    {
        $pasos = [
            ['nombre' => 'Registro', 'completado' => true],
            ['nombre' => 'Requisitos', 'completado' => $postulante?->cumpleRequisitos() ?? false],
            ['nombre' => 'Pago', 'completado' => $postulante?->tienePagoConfirmado() ?? false],
            ['nombre' => 'Grupo Asignado', 'completado' => $postulante?->id_grupo !== null],
            ['nombre' => 'Cursando', 'completado' => $postulante?->notas->isNotEmpty() ?? false],
        ];

        return $pasos;
    }
}
