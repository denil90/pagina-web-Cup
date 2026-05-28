<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aula;
use App\Models\Grupo;
use App\Models\Horario;
use App\Models\Turno;
use App\Services\GrupoService;
use Illuminate\Http\Request;

class GrupoController extends Controller
{
    public function __construct(
        private GrupoService $grupoService
    ) {}

    public function index()
    {
        $grupos = Grupo::with(['horario', 'aula', 'turno'])
            ->withCount('postulantes')
            ->get();

        return view('admin.grupos.index', compact('grupos'));
    }

    public function create()
    {
        $horarios = Horario::all();
        $aulas = Aula::all();
        $turnos = Turno::all();

        return view('admin.grupos.create', compact('horarios', 'aulas', 'turnos'));
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
        return view('admin.grupos.show', compact('estadisticas'));
    }

    public function edit(int $id)
    {
        $grupo = Grupo::findOrFail($id);
        $horarios = Horario::all();
        $aulas = Aula::all();
        $turnos = Turno::all();

        return view('admin.grupos.edit', compact('grupo', 'horarios', 'aulas', 'turnos'));
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
