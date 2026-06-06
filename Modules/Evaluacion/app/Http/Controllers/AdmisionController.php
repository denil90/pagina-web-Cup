<?php

namespace Modules\Evaluacion\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Academico\Models\Gestion;
use Modules\Evaluacion\Services\AdmisionService;
use Illuminate\Http\Request;

class AdmisionController extends Controller
{
    public function __construct(
        private readonly AdmisionService $admisionService
    ) {}

    public function index()
    {
        $gestiones = Gestion::orderByDesc('anio')->orderByDesc('semestre')->get();
        return view('evaluacion::admision.index', compact('gestiones'));
    }

    /**
     * Ejecuta el procedimiento almacenado de admisión.
     */
    public function procesar(Request $request)
    {
        $request->validate([
            'id_gestion' => 'required|exists:gestion,id_gestion',
        ]);

        $resultado = $this->admisionService->procesarAdmision($request->id_gestion);

        if ($resultado['exito']) {
            return redirect()->route('admin.admision.resultados', $request->id_gestion)
                ->with('success', "Admisión procesada: {$resultado['total_admitidos']} admitidos de {$resultado['total_postulantes']} postulantes.");
        }

        return back()->with('error', 'Error en el proceso: ' . $resultado['error']);
    }

    public function resultados(int $gestionId)
    {
        $gestion = Gestion::findOrFail($gestionId);
        $admitidos = $this->admisionService->obtenerResultados($gestionId);

        return view('evaluacion::admision.resultados', compact('gestion', 'admitidos'));
    }
}
