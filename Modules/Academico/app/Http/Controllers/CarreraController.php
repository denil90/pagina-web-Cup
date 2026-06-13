<?php

namespace Modules\Academico\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Academico\Domain\DTOs\CarreraDTO;
use Modules\Academico\Domain\UseCases\CreateCarreraUseCase;
use Modules\Academico\Domain\UseCases\DeleteCarreraUseCase;
use Modules\Academico\Domain\UseCases\UpdateCarreraUseCase;
use Modules\Academico\Http\Requests\StoreCarreraRequest;
use Modules\Academico\Models\Carrera;

class CarreraController extends Controller
{
    public function __construct(
        private readonly CreateCarreraUseCase $createCarrera,
        private readonly UpdateCarreraUseCase $updateCarrera,
        private readonly DeleteCarreraUseCase $deleteCarrera,
    ) {}

    public function index(\Illuminate\Http\Request $request)
    {
        $gestiones = \Illuminate\Support\Facades\DB::table('gestion')
            ->orderBy('anio', 'desc')
            ->orderBy('semestre', 'desc')
            ->get();

        $id_gestion = $request->input('id_gestion');
        if (!$id_gestion && $gestiones->isNotEmpty()) {
            $id_gestion = $gestiones->first()->id_gestion;
        }

        $carreras = Carrera::all();
        return view('academico::carreras.index', compact('carreras', 'gestiones', 'id_gestion'));
    }

    public function create()
    {
        return view('academico::carreras.create');
    }

    public function store(StoreCarreraRequest $request)
    {
        $dto = new CarreraDTO(
            nombre:      $request->validated('nombre'),
            descripcion: $request->validated('descripcion'),
            cupoMaximo:  (int) $request->validated('cupo_maximo'),
        );

        try {
            $this->createCarrera->execute($dto);
            return redirect()->route('admin.carreras.index')
                ->with('success', 'Carrera creada exitosamente.');
        } catch (\RuntimeException|\InvalidArgumentException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(int $id)
    {
        $carrera = Carrera::findOrFail($id);
        return view('academico::carreras.edit', compact('carrera'));
    }

    public function update(StoreCarreraRequest $request, int $id)
    {
        $dto = new CarreraDTO(
            nombre:      $request->validated('nombre'),
            descripcion: $request->validated('descripcion'),
            cupoMaximo:  (int) $request->validated('cupo_maximo'),
        );

        try {
            $this->updateCarrera->execute($id, $dto);
            return redirect()->route('admin.carreras.index')
                ->with('success', 'Carrera actualizada.');
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function destroy(int $id)
    {
        try {
            $this->deleteCarrera->execute($id);
            return redirect()->route('admin.carreras.index')
                ->with('success', 'Carrera eliminada.');
        } catch (\Exception $e) {
            return back()->with('error', 'No se puede eliminar: ' . $e->getMessage());
        }
    }
}
