<?php

namespace Modules\Evaluacion\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Evaluacion\Http\Requests\RegistrarNotaRequest;
use Modules\Planificacion\Models\Grupo;
use Modules\Academico\Models\Materia;
use Modules\Evaluacion\Models\Nota;
use Modules\Admision\Models\Postulante;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotaController extends Controller
{
    public function index(Request $request)
    {
        $grupos = Grupo::with('turno')->get();
        $materias = Materia::all();
        $postulantes = collect();

        if ($request->filled('id_grupo')) {
            $query = Postulante::with(['usuario', 'notas.materia'])
                ->where('id_grupo', $request->id_grupo);

            $postulantes = $query->get();
        }

        return view('evaluacion::notas.index', compact('grupos', 'materias', 'postulantes'));
    }

    public function registrar(int $postulanteId, int $materiaId)
    {
        $postulante = Postulante::with('usuario')->findOrFail($postulanteId);
        $materia = Materia::findOrFail($materiaId);
        $nota = Nota::where('id_postulante', $postulanteId)
            ->where('id_materia', $materiaId)
            ->first();

        return view('evaluacion::notas.registrar', compact('postulante', 'materia', 'nota'));
    }

    /**
     * Guarda o actualiza notas.
     */
    public function guardar(RegistrarNotaRequest $request)
    {
        try {
            $existe = Nota::where('id_postulante', $request->id_postulante)
                ->where('id_materia', $request->id_materia)
                ->exists();

            if ($existe) {
                DB::table('notas')
                    ->where('id_postulante', $request->id_postulante)
                    ->where('id_materia', $request->id_materia)
                    ->update([
                        'examen1' => $request->examen1,
                        'examen2' => $request->examen2,
                        'examen3' => $request->examen3,
                    ]);
            } else {
                DB::table('notas')->insert([
                    'id_postulante' => $request->id_postulante,
                    'id_materia' => $request->id_materia,
                    'examen1' => $request->examen1,
                    'examen2' => $request->examen2,
                    'examen3' => $request->examen3,
                ]);
            }

            return back()->with('success', 'Notas registradas exitosamente. El promedio fue calculado automáticamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al registrar notas: ' . $e->getMessage());
        }
    }
}
