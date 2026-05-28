<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gestion;
use Illuminate\Http\Request;

class GestionController extends Controller
{
    public function index()
    {
        $gestiones = Gestion::orderByDesc('anio')->orderByDesc('semestre')->get();
        return view('admin.gestiones.index', compact('gestiones'));
    }

    public function create()
    {
        return view('admin.gestiones.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'semestre' => 'required|string|max:20',
            'anio' => 'required|integer|min:2020|max:2050',
        ]);

        try {
            Gestion::create($request->only('semestre', 'anio'));
            return redirect()->route('admin.gestiones.index')
                ->with('success', 'Gestión creada exitosamente.');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Error al crear la gestión: ' . $e->getMessage());
        }
    }

    public function edit(int $id)
    {
        $gestion = Gestion::findOrFail($id);
        return view('admin.gestiones.edit', compact('gestion'));
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'semestre' => 'required|string|max:20',
            'anio' => 'required|integer|min:2020|max:2050',
        ]);

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
