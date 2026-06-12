<?php

namespace Modules\Facultad\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Facultad\Http\Requests\StoreDocenteRequest;
use Modules\Facultad\Models\Docente;
use Modules\Planificacion\Models\Grupo;
use Modules\Academico\Models\Materia;
use Modules\Seguridad\Models\Usuario;
use Modules\Facultad\Services\DocenteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DocenteController extends Controller
{
    public function __construct(
        private readonly DocenteService $docenteService
    ) {}

    public function index(Request $request)
    {
        $query = Docente::with('usuario');
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }
        $docentes = $query->get();
        return view('facultad::docentes.index', compact('docentes'));
    }

    public function create()
    {
        return view('facultad::docentes.create');
    }

    public function store(StoreDocenteRequest $request)
    {
        try {
            DB::beginTransaction();

            $usuario = Usuario::create([
                'nombre' => $request->nombre,
                'apellidos' => $request->apellidos,
                'ci' => $request->ci,
                'contrasena' => $request->contrasena,
                'fechanac' => $request->fechanac,
                'sexo' => $request->sexo,
                'direccion' => $request->direccion,
                'telefono' => $request->telefono,
                'rol' => 'docente',
                'correo' => $request->correo,
            ]);

            Docente::create([
                'id_docente' => $usuario->id_usuario,
                'titulo_profesional' => $request->titulo_profesional,
                'maestria' => $request->maestria,
                'diplomado' => $request->diplomado,
                'estado' => 'ACTIVO',
            ]);

            DB::commit();

            return redirect()->route('admin.docentes.index')
                ->with('success', 'Docente registrado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al registrar docente: ' . $e->getMessage());
        }
    }

    public function edit(int $id)
    {
        $docente = Docente::with('usuario')->findOrFail($id);
        return view('facultad::docentes.edit', compact('docente'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'titulo_profesional' => 'required|string|max:150',
            'maestria' => 'nullable|string|max:150',
            'diplomado' => 'nullable|string|max:150',
            'estado' => 'required|in:ACTIVO,INACTIVO,PENDIENTE,RECHAZADO',
        ]);

        try {
            DB::beginTransaction();

            $docente = Docente::findOrFail($id);
            $docente->update($request->only('titulo_profesional', 'maestria', 'diplomado', 'estado'));
            $docente->usuario->update($request->only('nombre', 'apellidos'));

            DB::commit();

            return redirect()->route('admin.docentes.index')
                ->with('success', 'Docente actualizado.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    public function showAsignar(int $id)
    {
        $docente = Docente::with(['usuario', 'gruposAsignados.grupo.horario', 'gruposAsignados.materia'])->findOrFail($id);
        $grupos = Grupo::with(['horario', 'turno', 'aula'])->get();
        $materias = Materia::all();
        $carga = $this->docenteService->obtenerCargaDocente($id);

        return view('facultad::docentes.asignar', compact('docente', 'grupos', 'materias', 'carga'));
    }

    public function asignar(Request $request, int $id)
    {
        $request->validate([
            'id_grupo' => 'required|exists:grupo,id_grupo',
            'id_materia' => 'required|exists:materia,id_materia',
        ]);

        try {
            $this->docenteService->asignarAGrupo($id, $request->id_grupo, $request->id_materia);
            return back()->with('success', 'Docente asignado al grupo exitosamente.');
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function removerAsignacion(int $asignacionId)
    {
        try {
            $this->docenteService->removerDeGrupo($asignacionId);
            return back()->with('success', 'Asignación removida.');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al remover: ' . $e->getMessage());
        }
    }

    public function show(int $id)
    {
        $docente = Docente::with('usuario')->findOrFail($id);
        return view('facultad::docentes.show', compact('docente'));
    }

    public function aprobar(int $id)
    {
        $docente = Docente::findOrFail($id);
        $docente->update(['estado' => 'ACTIVO']);

        return redirect()->route('admin.docentes.show', $id)
            ->with('success', 'La postulación docente ha sido APROBADA exitosamente.');
    }

    public function rechazar(int $id)
    {
        $docente = Docente::findOrFail($id);
        $docente->update(['estado' => 'RECHAZADO']);

        return redirect()->route('admin.docentes.show', $id)
            ->with('success', 'La postulación docente ha sido RECHAZADA.');
    }
}
