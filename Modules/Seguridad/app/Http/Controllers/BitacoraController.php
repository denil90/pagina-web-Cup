<?php

namespace Modules\Seguridad\Http\Controllers;

use App\Http\Controllers\Controller;
use Modules\Seguridad\Models\Bitacora;
use Illuminate\Http\Request;

class BitacoraController extends Controller
{
    public function index(Request $request)
    {
        $query = Bitacora::with('usuario')->orderByDesc('created_at');

        if ($request->filled('modulo')) {
            $query->where('modulo', $request->modulo);
        }
        if ($request->filled('accion')) {
            $query->where('accion', $request->accion);
        }

        $logs = $query->paginate(50);
        $modulos = Bitacora::select('modulo')->distinct()->pluck('modulo');
        $acciones = Bitacora::select('accion')->distinct()->pluck('accion');

        return view('seguridad::bitacora.index', compact('logs', 'modulos', 'acciones'));
    }
}
