<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gestion;
use App\Services\AdmisionService;
use Illuminate\Http\Request;

class AdmisionController extends Controller
{
    public function __construct(
        private AdmisionService $admisionService
    ) {}

    public function index()
    {
        $gestiones = Gestion::orderByDesc('anio')->orderByDesc('semestre')->get();
        return view('admin.admision.index', compact('gestiones'));
    }

    /**
     * Ejecuta el procedimiento almacenado de admisión.
     * Es una operación destructiva: borra admisiones previas de esa gestión.
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

        return view('admin.admision.resultados', compact('gestion', 'admitidos'));
    }
}
