<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdmisionFinal;
use App\Models\Docente;
use App\Models\Gestion;
use App\Models\Grupo;
use App\Models\Postulante;

class DashboardController extends Controller
{
    public function index()
    {
        $gestionActual = Gestion::orderByDesc('anio')->orderByDesc('semestre')->first();

        $estadisticas = [
            'total_postulantes' => $gestionActual
                ? Postulante::where('id_gestion', $gestionActual->id_gestion)->count()
                : 0,
            'total_grupos' => Grupo::count(),
            'total_docentes' => Docente::where('estado', 'ACTIVO')->count(),
            'total_admitidos' => $gestionActual
                ? AdmisionFinal::whereHas('postulante', fn($q) => $q->where('id_gestion', $gestionActual->id_gestion))->count()
                : 0,
        ];

        $gestiones = Gestion::orderByDesc('anio')->orderByDesc('semestre')->get();

        return view('admin.dashboard', compact('estadisticas', 'gestionActual', 'gestiones'));
    }
}
