<?php

namespace App\Http\Controllers\Postulante;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class RequisitoController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();
        $postulante = $usuario->postulante;

        return view('postulante.requisitos', compact('usuario', 'postulante'));
    }

    public function subirTitulo(Request $request)
    {
        $request->validate([
            'archivo_titulo_bachiller' => 'required|file|mimes:pdf|max:5120',
        ], [
            'archivo_titulo_bachiller.required' => 'Debe seleccionar un archivo PDF.',
            'archivo_titulo_bachiller.mimes' => 'El archivo debe ser un PDF.',
            'archivo_titulo_bachiller.max' => 'El archivo no debe superar los 5 MB.',
        ]);

        $postulante = Auth::user()->postulante;

        // Eliminar archivo anterior si existe
        if ($postulante->archivo_titulo_bachiller) {
            Storage::disk('public')->delete($postulante->archivo_titulo_bachiller);
        }

        // Guardar el nuevo archivo
        $ruta = $request->file('archivo_titulo_bachiller')
            ->store('requisitos/' . $postulante->id_postulante, 'public');

        $postulante->update([
            'archivo_titulo_bachiller' => $ruta,
        ]);

        return back()->with('success', 'Título de Bachiller subido correctamente.');
    }

    public function subirLibreta(Request $request)
    {
        $request->validate([
            'archivo_libreta' => 'required|file|mimes:pdf|max:5120',
        ], [
            'archivo_libreta.required' => 'Debe seleccionar un archivo PDF.',
            'archivo_libreta.mimes' => 'El archivo debe ser un PDF.',
            'archivo_libreta.max' => 'El archivo no debe superar los 5 MB.',
        ]);

        $postulante = Auth::user()->postulante;

        // Eliminar archivo anterior si existe
        if ($postulante->archivo_libreta) {
            Storage::disk('public')->delete($postulante->archivo_libreta);
        }

        // Guardar el nuevo archivo
        $ruta = $request->file('archivo_libreta')
            ->store('requisitos/' . $postulante->id_postulante, 'public');

        $postulante->update([
            'archivo_libreta' => $ruta,
        ]);

        return back()->with('success', 'Libreta de Último Año subida correctamente.');
    }
}
