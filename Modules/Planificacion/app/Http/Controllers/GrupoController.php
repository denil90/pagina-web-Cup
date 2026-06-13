<?php

namespace Modules\Planificacion\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Planificacion\Models\Aula;
use Modules\Planificacion\Models\Grupo;
use Modules\Planificacion\Models\Horario;
use Modules\Planificacion\Models\Turno;
use Modules\Planificacion\Services\GrupoService;
use Illuminate\Http\Request;

class GrupoController extends Controller
{
    public function __construct(
        private readonly GrupoService $grupoService
    ) {}

    public function index(Request $request)
    {
        $gestiones = \Illuminate\Support\Facades\DB::table('gestion')
            ->orderBy('anio', 'desc')
            ->orderBy('semestre', 'desc')
            ->get();

        $id_gestion = $request->input('id_gestion');
        if (!$id_gestion && $gestiones->isNotEmpty()) {
            $id_gestion = $gestiones->first()->id_gestion;
        }

        $grupos = Grupo::with(['horario', 'aula', 'turno'])
            ->withCount(['postulantes' => function($query) use ($id_gestion) {
                if ($id_gestion) {
                    $query->where('id_gestion', $id_gestion);
                }
            }])
            ->get();

        return view('planificacion::grupos.index', compact('grupos', 'gestiones', 'id_gestion'));
    }

    public function create()
    {
        $horarios = Horario::all();
        $aulas = Aula::all();
        $turnos = Turno::all();

        return view('planificacion::grupos.create', compact('horarios', 'aulas', 'turnos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'capacidad_maxima' => 'required|integer|min:1|max:70',
            'id_horario' => 'required|exists:horario,id_horario',
            'id_aula' => 'required|exists:aula,id_aula',
            'id_turno' => 'required|exists:turno,id_turno',
        ]);

        $aulaOcupada = Grupo::where('id_aula', $request->id_aula)
            ->where('id_horario', $request->id_horario)
            ->exists();

        if ($aulaOcupada) {
            return back()->withInput()->withErrors([
                'id_aula' => 'El aula seleccionada ya está ocupada en este horario.'
            ]);
        }

        try {
            Grupo::create($request->only('nombre', 'capacidad_maxima', 'id_horario', 'id_aula', 'id_turno'));
            return redirect()->route('admin.grupos.index')
                ->with('success', 'Grupo creado exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al crear grupo: ' . $e->getMessage());
        }
    }

    public function show(int $id)
    {
        $estadisticas = $this->grupoService->obtenerEstadisticasGrupo($id);
        return view('planificacion::grupos.show', compact('estadisticas'));
    }

    public function edit(int $id)
    {
        $grupo = Grupo::findOrFail($id);
        $horarios = Horario::all();
        $aulas = Aula::all();
        $turnos = Turno::all();

        return view('planificacion::grupos.edit', compact('grupo', 'horarios', 'aulas', 'turnos'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'capacidad_maxima' => 'required|integer|min:1|max:70',
            'id_horario' => 'required|exists:horario,id_horario',
            'id_aula' => 'required|exists:aula,id_aula',
            'id_turno' => 'required|exists:turno,id_turno',
        ]);

        $aulaOcupada = Grupo::where('id_aula', $request->id_aula)
            ->where('id_horario', $request->id_horario)
            ->where('id_grupo', '!=', $id)
            ->exists();

        if ($aulaOcupada) {
            return back()->withInput()->withErrors([
                'id_aula' => 'El aula seleccionada ya está ocupada en este horario.'
            ]);
        }

        try {
            Grupo::findOrFail($id)->update($request->only('nombre', 'capacidad_maxima', 'id_horario', 'id_aula', 'id_turno'));
            return redirect()->route('admin.grupos.index')
                ->with('success', 'Grupo actualizado.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroy(int $id)
    {
        try {
            Grupo::findOrFail($id)->delete();
            return redirect()->route('admin.grupos.index')
                ->with('success', 'Grupo eliminado.');
        } catch (\Exception $e) {
            return back()->with('error', 'No se puede eliminar: ' . $e->getMessage());
        }
    }
}
