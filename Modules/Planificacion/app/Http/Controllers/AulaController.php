<?php

namespace Modules\Planificacion\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Planificacion\Models\Aula;
use Illuminate\Http\Request;

class AulaController extends Controller
{
    public function index()
    {
        $aulas = Aula::all();
        return view('planificacion::configuracion.aulas', compact('aulas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50',
            'edificio' => 'required|string|max:50',
            'capacidad' => 'required|integer|min:1',
        ]);

        try {
            Aula::create($request->only('nombre', 'edificio', 'capacidad'));
            return back()->with('success', 'Aula creada.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:50',
            'edificio' => 'required|string|max:50',
            'capacidad' => 'required|integer|min:1',
        ]);

        try {
            $aula = Aula::findOrFail($id);
            $aula->update($request->only('nombre', 'edificio', 'capacidad'));
            return back()->with('success', 'Aula actualizada.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroy(int $id)
    {
        try {
            Aula::findOrFail($id)->delete();
            return back()->with('success', 'Aula eliminada.');
        } catch (\Exception $e) {
            return back()->with('error', 'No se puede eliminar: ' . $e->getMessage());
        }
    }
}
