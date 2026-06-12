<?php

namespace Modules\Facultad\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Modules\Planificacion\Models\Grupo;
use Modules\Academico\Models\Materia;
use Modules\Admision\Models\Postulante;

class DocenteDashboardController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();
        $docente = $usuario->docente;

        return view('facultad::docente-dashboard', compact('usuario', 'docente'));
    }

    public function verEstudiantes(int $grupoId, int $materiaId)
    {
        $usuario = Auth::user();
        $docente = $usuario->docente;

        $estaAsignado = $docente->gruposAsignados()
            ->where('id_grupo', $grupoId)
            ->where('id_materia', $materiaId)
            ->exists();

        if (!$estaAsignado) {
            abort(403, 'No está asignado a esta materia/grupo.');
        }

        $grupo = Grupo::findOrFail($grupoId);
        $materia = Materia::findOrFail($materiaId);

        // Obtener postulantes en este grupo, incluyendo sus notas para esta materia
        $postulantes = Postulante::with(['usuario', 'notas' => function ($query) use ($materiaId) {
            $query->where('id_materia', $materiaId);
        }])->where('id_grupo', $grupoId)->get();

        return view('facultad::docente-estudiantes', compact('grupo', 'materia', 'postulantes'));
    }

    public function registrarNota(int $postulanteId, int $materiaId)
    {
        $usuario = Auth::user();
        $docente = $usuario->docente;

        $postulante = Postulante::with('usuario')->findOrFail($postulanteId);
        
        // Verificar que el postulante pertenece a un grupo asignado al docente para esta materia
        $estaAsignado = $docente->gruposAsignados()
            ->where('id_grupo', $postulante->id_grupo)
            ->where('id_materia', $materiaId)
            ->exists();

        if (!$estaAsignado) {
            abort(403, 'No está asignado a esta materia/grupo de este estudiante.');
        }

        $materia = Materia::findOrFail($materiaId);
        
        $nota = DB::table('notas')
            ->where('id_postulante', $postulanteId)
            ->where('id_materia', $materiaId)
            ->first();

        return view('facultad::docente-registrar-nota', compact('postulante', 'materia', 'nota'));
    }

    public function guardarNota(Request $request)
    {
        $request->validate([
            'id_postulante' => 'required|exists:postulante,id_postulante',
            'id_materia' => 'required|exists:materia,id_materia',
            'examen1' => 'nullable|numeric|min:0|max:100',
            'examen2' => 'nullable|numeric|min:0|max:100',
            'examen3' => 'nullable|numeric|min:0|max:100',
        ], [
            'examen1.max' => 'La nota no puede superar 100 puntos.',
            'examen2.max' => 'La nota no puede superar 100 puntos.',
            'examen3.max' => 'La nota no puede superar 100 puntos.',
        ]);

        $usuario = Auth::user();
        $docente = $usuario->docente;

        $postulante = Postulante::findOrFail($request->id_postulante);
        
        // Verificar asignación
        $estaAsignado = $docente->gruposAsignados()
            ->where('id_grupo', $postulante->id_grupo)
            ->where('id_materia', $request->id_materia)
            ->exists();

        if (!$estaAsignado) {
            abort(403, 'No está autorizado a registrar notas en este grupo/materia.');
        }

        try {
            $existe = DB::table('notas')
                ->where('id_postulante', $request->id_postulante)
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

            return redirect()->route('docente.grupos.materia.estudiantes', [$postulante->id_grupo, $request->id_materia])
                ->with('success', 'Notas registradas exitosamente para el estudiante ' . $postulante->usuario->nombreCompleto);
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al guardar notas: ' . $e->getMessage());
        }
    }
}
