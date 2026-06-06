<?php

namespace Modules\Evaluacion\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Evaluacion\Models\AdmisionFinal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResultadoController extends Controller
{
    public function misNotas()
    {
        $postulante = Auth::user()->postulante()->with('notas.materia')->first();
        return view('admision::postulante.notas', compact('postulante'));
    }

    public function misResultados()
    {
        $postulante = Auth::user()->postulante;
        $admision = AdmisionFinal::with('carrera')
            ->where('id_postulante', $postulante->id_postulante)
            ->first();

        return view('admision::postulante.resultados', compact('postulante', 'admision'));
    }

    /**
     * Consulta pública de resultados por CI (sin autenticación).
     */
    public function consultaPublica(Request $request)
    {
        $resultado = null;

        if ($request->filled('ci')) {
            $resultado = AdmisionFinal::with(['postulante.usuario', 'carrera'])
                ->whereHas('postulante.usuario', fn($q) => $q->where('ci', $request->ci))
                ->first();
        }

        return view('evaluacion::public.resultados', compact('resultado'));
    }
}
