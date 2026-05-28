<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aula;
use App\Models\Horario;
use App\Models\Turno;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    // --- AULAS ---
    public function aulasIndex()
    {
        $aulas = Aula::all();
        return view('admin.configuracion.aulas', compact('aulas'));
    }

    public function aulasStore(Request $request)
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

    public function aulasDestroy(int $id)
    {
        try {
            Aula::findOrFail($id)->delete();
            return back()->with('success', 'Aula eliminada.');
        } catch (\Exception $e) {
            return back()->with('error', 'No se puede eliminar: ' . $e->getMessage());
        }
    }

    // --- HORARIOS ---
    public function horariosIndex()
    {
        $horarios = Horario::all();
        return view('admin.configuracion.horarios', compact('horarios'));
    }

    public function horariosStore(Request $request)
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

    public function horariosDestroy(int $id)
    {
        try {
            Horario::findOrFail($id)->delete();
            return back()->with('success', 'Horario eliminado.');
        } catch (\Exception $e) {
            return back()->with('error', 'No se puede eliminar: ' . $e->getMessage());
        }
    }

    // --- TURNOS ---
    public function turnosIndex()
    {
        $turnos = Turno::all();
        return view('admin.configuracion.turnos', compact('turnos'));
    }

    public function turnosStore(Request $request)
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

    public function turnosDestroy(int $id)
    {
        try {
            Turno::findOrFail($id)->delete();
            return back()->with('success', 'Turno eliminado.');
        } catch (\Exception $e) {
            return back()->with('error', 'No se puede eliminar: ' . $e->getMessage());
        }
    }
}
