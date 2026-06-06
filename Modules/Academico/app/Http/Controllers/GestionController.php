<?php

namespace Modules\Academico\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Academico\Domain\DTOs\GestionDTO;
use Modules\Academico\Domain\UseCases\CreateGestionUseCase;
use Modules\Academico\Http\Requests\StoreGestionRequest;
use Modules\Academico\Models\Gestion;

class GestionController extends Controller
{
    public function __construct(
        private readonly CreateGestionUseCase $createGestion,
    ) {}

    public function index()
    {
        $gestiones = Gestion::orderByDesc('anio')->orderByDesc('semestre')->get();
        return view('academico::gestiones.index', compact('gestiones'));
    }

    public function create()
    {
        return view('academico::gestiones.create');
    }

    public function store(StoreGestionRequest $request)
    {
        $dto = new GestionDTO(
            semestre: $request->validated('semestre'),
            anio:     (int) $request->validated('anio'),
        );

        try {
            $this->createGestion->execute($dto);
            return redirect()->route('admin.gestiones.index')
                ->with('success', 'Gestión creada exitosamente.');
        } catch (\RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function edit(int $id)
    {
        $gestion = Gestion::findOrFail($id);
        return view('academico::gestiones.edit', compact('gestion'));
    }

    public function update(StoreGestionRequest $request, int $id)
    {
        try {
            $gestion = Gestion::findOrFail($id);
            $gestion->update($request->only('semestre', 'anio'));
            return redirect()->route('admin.gestiones.index')
                ->with('success', 'Gestión actualizada exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    public function destroy(int $id)
    {
        try {
            Gestion::findOrFail($id)->delete();
            return redirect()->route('admin.gestiones.index')
                ->with('success', 'Gestión eliminada.');
        } catch (\Exception $e) {
            return back()->with('error', 'No se puede eliminar: ' . $e->getMessage());
        }
    }
}
