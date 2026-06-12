<?php

namespace Modules\Facultad\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DocenteRequisitoController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();
        $docente = $usuario->docente;

        return view('facultad::docente-requisitos', compact('usuario', 'docente'));
    }

    public function subirTitulo(Request $request)
    {
        $docente = Auth::user()->docente;
        
        $request->validate([
            'titulo_profesional' => 'required|string|max:150',
            'archivo_titulo'     => $docente->archivo_titulo ? 'nullable|file|mimes:pdf|max:5120' : 'required|file|mimes:pdf|max:5120',
        ], [
            'titulo_profesional.required' => 'El nombre del título profesional es obligatorio.',
            'archivo_titulo.required'     => 'Debe seleccionar un archivo PDF.',
            'archivo_titulo.mimes'        => 'El archivo debe ser un PDF.',
            'archivo_titulo.max'          => 'El archivo no debe superar los 5MB.',
        ]);

        $updateData = ['titulo_profesional' => $request->titulo_profesional];
        if ($request->hasFile('archivo_titulo')) {
            $path = $request->file('archivo_titulo')->store('requisitos/docentes', 'public');
            $updateData['archivo_titulo'] = $path;
        }
        
        $docente->update($updateData);

        return back()->with('success', 'Título profesional guardado correctamente.');
    }

    public function subirMaestria(Request $request)
    {
        $docente = Auth::user()->docente;

        $request->validate([
            'maestria'         => 'required|string|max:150',
            'archivo_maestria' => $docente->archivo_maestria ? 'nullable|file|mimes:pdf|max:5120' : 'required|file|mimes:pdf|max:5120',
        ], [
            'maestria.required'         => 'El nombre de la maestría es obligatorio.',
            'archivo_maestria.required' => 'Debe seleccionar un archivo PDF.',
            'archivo_maestria.mimes'    => 'El archivo debe ser un PDF.',
            'archivo_maestria.max'      => 'El archivo no debe superar los 5MB.',
        ]);

        $updateData = ['maestria' => $request->maestria];
        if ($request->hasFile('archivo_maestria')) {
            $path = $request->file('archivo_maestria')->store('requisitos/docentes', 'public');
            $updateData['archivo_maestria'] = $path;
        }

        $docente->update($updateData);

        return back()->with('success', 'Certificado de maestría guardado correctamente.');
    }

    public function subirDiplomado(Request $request)
    {
        $docente = Auth::user()->docente;

        $request->validate([
            'diplomado'         => 'required|string|max:150',
            'archivo_diplomado' => $docente->archivo_diplomado ? 'nullable|file|mimes:pdf|max:5120' : 'required|file|mimes:pdf|max:5120',
        ], [
            'diplomado.required'         => 'El nombre del diplomado es obligatorio.',
            'archivo_diplomado.required' => 'Debe seleccionar un archivo PDF.',
            'archivo_diplomado.mimes'    => 'El archivo debe ser un PDF.',
            'archivo_diplomado.max'      => 'El archivo no debe superar los 5MB.',
        ]);

        $updateData = ['diplomado' => $request->diplomado];
        if ($request->hasFile('archivo_diplomado')) {
            $path = $request->file('archivo_diplomado')->store('requisitos/docentes', 'public');
            $updateData['archivo_diplomado'] = $path;
        }

        $docente->update($updateData);

        return back()->with('success', 'Certificado de diplomado guardado correctamente.');
    }
}
