<?php

namespace App\Http\Controllers\Postulante;

use App\Http\Controllers\Controller;
use App\Models\AdmisionFinal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResultadoController extends Controller
{
    public function misNotas()
    {
        $postulante = Auth::user()->postulante()->with('notas.materia')->first();
        return view('postulante.notas', compact('postulante'));
    }

    public function misResultados()
    {
        $postulante = Auth::user()->postulante;
        $admision = AdmisionFinal::with('carrera')
            ->where('id_postulante', $postulante->id_postulante)
            ->first();

        return view('postulante.resultados', compact('postulante', 'admision'));
    }

    /**
     * Consulta pública de resultados por CI (sin autenticación).
     */
    public static function consultaPublica(Request $request)
    {
        $resultado = null;

        if ($request->filled('ci')) {
            $resultado = AdmisionFinal::with(['postulante.usuario', 'carrera'])
                ->whereHas('postulante.usuario', fn($q) => $q->where('ci', $request->ci))
                ->first();
        }

        return view('public.resultados', compact('resultado'));
    }
}
