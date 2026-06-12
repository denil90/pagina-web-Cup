<?php

namespace Modules\Admision\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Academico\Models\Gestion;
use Modules\Planificacion\Models\Grupo;
use Modules\Admision\Models\Postulante;
use Modules\Planificacion\Services\GrupoService;
use Illuminate\Http\Request;

class PostulanteAdminController extends Controller
{
    public function __construct(
        private readonly GrupoService $grupoService
    ) {}

    public function index(Request $request)
    {
        $query = Postulante::with(['usuario', 'carreraPrimera', 'carreraSegunda', 'grupo', 'gestion', 'pago']);

        if ($request->filled('id_gestion')) {
            $query->where('id_gestion', $request->id_gestion);
        }

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->whereHas('usuario', function ($q) use ($buscar) {
                $q->where('nombre', 'ILIKE', "%{$buscar}%")
                  ->orWhere('apellidos', 'ILIKE', "%{$buscar}%")
                  ->orWhere('ci', 'ILIKE', "%{$buscar}%");
            });
        }

        $postulantes = $query->paginate(20);
        $gestiones = Gestion::orderByDesc('anio')->orderByDesc('semestre')->get();

        return view('admision::postulantes.index', compact('postulantes', 'gestiones'));
    }

    public function show(int $id)
    {
        $postulante = Postulante::with([
            'usuario', 'carreraPrimera', 'carreraSegunda',
            'grupo.horario', 'grupo.turno', 'gestion',
            'notas.materia', 'pago', 'admisionFinal.carrera'
        ])->findOrFail($id);

        return view('admision::postulantes.show', compact('postulante'));
    }

    public function verificarRequisitos(Request $request, int $id)
    {
        $request->validate([
            'titulo_bachiller' => 'required|boolean',
            'libreta_de_ultimo_anio' => 'required|boolean',
        ]);

        try {
            $postulante = Postulante::with('pago')->findOrFail($id);
            $postulante->update([
                'titulo_bachiller' => $request->titulo_bachiller,
                'libreta_de_ultimo_anio' => $request->libreta_de_ultimo_anio,
            ]);

            // Intentar asignación automática de grupo si ambas condiciones se cumplen
            $grupo = $this->grupoService->intentarAsignacionAutomatica($postulante->fresh()->load('pago'));

            $mensaje = 'Requisitos actualizados.';
            if ($grupo) {
                $mensaje .= " El postulante fue asignado automáticamente al grupo {$grupo->nombre}.";
            }

            return back()->with('success', $mensaje);
        } catch (\Exception $e) {
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function asignarGrupo(Request $request, int $id)
    {
        $request->validate([
            'id_grupo' => 'required|exists:grupo,id_grupo',
        ]);

        try {
            $postulante = Postulante::findOrFail($id);
            $this->grupoService->asignarPostulanteAGrupo($postulante, $request->id_grupo);
            return back()->with('success', 'Grupo asignado exitosamente.');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
