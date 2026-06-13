<?php

namespace Modules\Academico\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Academico\Domain\DTOs\MateriaDTO;
use Modules\Academico\Domain\UseCases\CreateMateriaUseCase;
use Modules\Academico\Domain\UseCases\UpdateMateriaUseCase;
use Modules\Academico\Http\Requests\StoreMateriaRequest;
use Modules\Academico\Models\Materia;
//controlador para gestionar las materias, se pueden crear, editar y eliminar materias
class MateriaController extends Controller
{
    public function __construct(
        private readonly CreateMateriaUseCase $createMateria,
        private readonly UpdateMateriaUseCase $updateMateria,
    ) {}
//mostrar la lista de materias, se ordena por nombre
    public function index()
    {
        $materias = Materia::all();
        return view('academico::materias.index', compact('materias'));
    }
//mostrar el formulario para crear una nueva materia
    public function create()
    {
        return view('academico::materias.create');
    }
//crear una materia, se captura la excepcion si el nombre de la materia ya existe o si los porcentajes no suman 100 y se muestra un mensaje de error
    public function store(StoreMateriaRequest $request)
    {
        $dto = new MateriaDTO(
            nombre:            $request->validated('nombre'),
            porcentajeExamen1: (float) $request->validated('porcentaje_examen1'),
            porcentajeExamen2: (float) $request->validated('porcentaje_examen2'),
            porcentajeExamen3: (float) $request->validated('porcentaje_examen3'),
        );

        try {
            $this->createMateria->execute($dto);
            return redirect()->route('admin.materias.index')
                ->with('success', 'Materia creada.');
        } catch (\InvalidArgumentException|\RuntimeException $e) {
            return back()->withInput()->withErrors(['porcentaje' => $e->getMessage()]);
        }
    }

    public function edit(int $id)
    {
        $materia = Materia::findOrFail($id);
        return view('academico::materias.edit', compact('materia'));
    }

    public function update(StoreMateriaRequest $request, int $id)
    {
        $dto = new MateriaDTO(
            nombre:            $request->validated('nombre'),
            porcentajeExamen1: (float) $request->validated('porcentaje_examen1'),
            porcentajeExamen2: (float) $request->validated('porcentaje_examen2'),
            porcentajeExamen3: (float) $request->validated('porcentaje_examen3'),
        );

        try {
            $this->updateMateria->execute($id, $dto);
            return redirect()->route('admin.materias.index')
                ->with('success', 'Materia actualizada.');
        } catch (\InvalidArgumentException|\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(int $id)
    {
        try {
            Materia::findOrFail($id)->delete();
            return redirect()->route('admin.materias.index')
                ->with('success', 'Materia eliminada.');
        } catch (\Exception $e) {
            return back()->with('error', 'No se puede eliminar: ' . $e->getMessage());
        }
    }
}
