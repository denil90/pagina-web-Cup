<?php

namespace Modules\Admision\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PostulanteDashboardController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();
        $postulante = $usuario->postulante()->with([
            'carreraPrimera', 'carreraSegunda', 'grupo.turno',
            'grupo.aula', 'grupo.docenteGrupos.materia',
            'grupo.docenteGrupos.docente.usuario',
            'grupo.docenteGrupos.horario',
            'gestion', 'notas.materia',
            'pago', 'admisionFinal.carrera',
            'turnoPreferido',
        ])->first();

        $estadoInscripcion = $this->determinarEstado($postulante);

        return view('admision::postulante.dashboard', compact('usuario', 'postulante', 'estadoInscripcion'));
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
