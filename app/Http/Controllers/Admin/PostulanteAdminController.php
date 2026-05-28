<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gestion;
use App\Models\Grupo;
use App\Models\Postulante;
use App\Services\GrupoService;
use Illuminate\Http\Request;

class PostulanteAdminController extends Controller
{
    public function __construct(
        private GrupoService $grupoService
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

        return view('admin.postulantes.index', compact('postulantes', 'gestiones'));
    }

    public function show(int $id)
    {
        $postulante = Postulante::with([
            'usuario', 'carreraPrimera', 'carreraSegunda',
            'grupo.horario', 'grupo.turno', 'gestion',
            'notas.materia', 'pago', 'admisionFinal.carrera'
        ])->findOrFail($id);

        return view('admin.postulantes.show', compact('postulante'));
    }

    public function verificarRequisitos(Request $request, int $id)
    {
        $request->validate([
            'titulo_bachiller' => 'required|boolean',
            'libreta_de_ultimo_anio' => 'required|boolean',
        ]);

        try {
            $postulante = Postulante::findOrFail($id);
            $postulante->update([
                'titulo_bachiller' => $request->titulo_bachiller,
                'libreta_de_ultimo_anio' => $request->libreta_de_ultimo_anio,
            ]);

            return back()->with('success', 'Requisitos actualizados.');
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
