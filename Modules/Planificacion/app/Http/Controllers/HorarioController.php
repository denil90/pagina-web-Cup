<?php

namespace Modules\Planificacion\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Planificacion\Models\Horario;
use Illuminate\Http\Request;

class HorarioController extends Controller
{
    public function index()
    {
        $horarios = Horario::all();
        return view('planificacion::configuracion.horarios', compact('horarios'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'dia' => 'required|string|max:20',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_final' => 'required|date_format:H:i|after:hora_inicio',
        ]);

        try {
            Horario::create($request->only('dia', 'hora_inicio', 'hora_final'));
            return back()->with('success', 'Horario creado.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'dia' => 'required|string|max:20',
            'hora_inicio' => 'required|date_format:H:i',
            'hora_final' => 'required|date_format:H:i|after:hora_inicio',
        ]);

        try {
            $horario = Horario::findOrFail($id);
            $horario->update($request->only('dia', 'hora_inicio', 'hora_final'));
            return back()->with('success', 'Horario actualizado.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroy(int $id)
    {
        try {
            Horario::findOrFail($id)->delete();
            return back()->with('success', 'Horario eliminado.');
        } catch (\Exception $e) {
            return back()->with('error', 'No se puede eliminar: ' . $e->getMessage());
        }
    }
}
