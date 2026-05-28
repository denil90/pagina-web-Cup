<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use Illuminate\Http\Request;

class CarreraController extends Controller
{
    public function index()
    {
        $carreras = Carrera::withCount('admitidos')->get();
        return view('admin.carreras.index', compact('carreras'));
    }

    public function create()
    {
        return view('admin.carreras.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'cupo_maximo' => 'required|integer|min:1',
        ]);

        try {
            Carrera::create($request->only('nombre', 'descripcion', 'cupo_maximo'));
            return redirect()->route('admin.carreras.index')
                ->with('success', 'Carrera creada exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al crear: ' . $e->getMessage());
        }
    }

    public function edit(int $id)
    {
        $carrera = Carrera::findOrFail($id);
        return view('admin.carreras.edit', compact('carrera'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:150',
            'descripcion' => 'nullable|string',
            'cupo_maximo' => 'required|integer|min:1',
        ]);

        try {
            Carrera::findOrFail($id)->update($request->only('nombre', 'descripcion', 'cupo_maximo'));
            return redirect()->route('admin.carreras.index')
                ->with('success', 'Carrera actualizada.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    public function destroy(int $id)
    {
        try {
            Carrera::findOrFail($id)->delete();
            return redirect()->route('admin.carreras.index')
                ->with('success', 'Carrera eliminada.');
        } catch (\Exception $e) {
            return back()->with('error', 'No se puede eliminar: ' . $e->getMessage());
        }
    }
}
