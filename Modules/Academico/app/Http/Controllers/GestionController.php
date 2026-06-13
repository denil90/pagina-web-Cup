<?php

namespace Modules\Academico\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Academico\Domain\DTOs\GestionDTO;
use Modules\Academico\Domain\UseCases\CreateGestionUseCase;
use Modules\Academico\Http\Requests\StoreGestionRequest;
use Modules\Academico\Models\Gestion;

class GestionController extends Controller
{//controlador para gestionar las gestiones, se pueden crear, editar y eliminar gestiones
    public function __construct(
        private readonly CreateGestionUseCase $createGestion,
    ) {}
//mostrar la lista de gestiones, se ordena por año y semestre
    public function index()
    {
        $gestiones = Gestion::orderByDesc('anio')->orderByDesc('semestre')->get();
        return view('academico::gestiones.index', compact('gestiones'));
    }
//mostrar el formulario para crear una nueva gestion
    public function create()
    {
        return view('academico::gestiones.create');
    }
// crear una gestion, se captura la excepcion si ya existe una gestion con el mismo año y semestre y se muestra un mensaje de error
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
//editar una gestion, mostrar el formulario con los datos actuales de la gestion
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
