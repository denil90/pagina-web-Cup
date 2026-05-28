<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Materia;
use Illuminate\Http\Request;

class MateriaController extends Controller
{
    public function index()
    {
        $materias = Materia::all();
        return view('admin.materias.index', compact('materias'));
    }

    public function create()
    {
        return view('admin.materias.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:150',
            'porcentaje_examen1' => 'required|numeric|min:0|max:100',
            'porcentaje_examen2' => 'required|numeric|min:0|max:100',
            'porcentaje_examen3' => 'required|numeric|min:0|max:100',
        ]);

        $total = $request->porcentaje_examen1 + $request->porcentaje_examen2 + $request->porcentaje_examen3;
        if (abs($total - 100) > 0.01) {
            return back()->withInput()->withErrors(['porcentaje' => 'Los porcentajes deben sumar 100%.']);
        }

        try {
            Materia::create($request->only('nombre', 'porcentaje_examen1', 'porcentaje_examen2', 'porcentaje_examen3'));
            return redirect()->route('admin.materias.index')->with('success', 'Materia creada.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function edit(int $id)
    {
        $materia = Materia::findOrFail($id);
        return view('admin.materias.edit', compact('materia'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:150',
            'porcentaje_examen1' => 'required|numeric|min:0|max:100',
            'porcentaje_examen2' => 'required|numeric|min:0|max:100',
            'porcentaje_examen3' => 'required|numeric|min:0|max:100',
        ]);

        try {
            Materia::findOrFail($id)->update($request->only('nombre', 'porcentaje_examen1', 'porcentaje_examen2', 'porcentaje_examen3'));
            return redirect()->route('admin.materias.index')->with('success', 'Materia actualizada.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function destroy(int $id)
    {
        try {
            Materia::findOrFail($id)->delete();
            return redirect()->route('admin.materias.index')->with('success', 'Materia eliminada.');
        } catch (\Exception $e) {
            return back()->with('error', 'No se puede eliminar: ' . $e->getMessage());
        }
    }
}
