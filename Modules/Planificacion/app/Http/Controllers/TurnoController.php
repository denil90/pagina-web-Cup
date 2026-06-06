<?php

namespace Modules\Planificacion\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Planificacion\Models\Turno;
use Illuminate\Http\Request;

class TurnoController extends Controller
{
    public function index()
    {
        $turnos = Turno::all();
        return view('planificacion::configuracion.turnos', compact('turnos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:50',
        ]);

        try {
            Turno::create($request->only('nombre'));
            return back()->with('success', 'Turno creado.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroy(int $id)
    {
        try {
            Turno::findOrFail($id)->delete();
            return back()->with('success', 'Turno eliminado.');
        } catch (\Exception $e) {
            return back()->with('error', 'No se puede eliminar: ' . $e->getMessage());
        }
    }
}
