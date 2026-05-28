<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gestion;
use App\Models\Grupo;
use App\Services\AdmisionService;
use App\Services\ReporteService;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    public function __construct(
        private ReporteService $reporteService,
        private AdmisionService $admisionService,
    ) {}

    public function index()
    {
        $gestiones = Gestion::orderByDesc('anio')->orderByDesc('semestre')->get();
        $grupos = Grupo::with('turno')->get();

        return view('admin.reportes.index', compact('gestiones', 'grupos'));
    }

    public function aprobadosPorGestion(Request $request)
    {
        $request->validate(['id_gestion' => 'required|exists:gestion,id_gestion']);

        $gestion = Gestion::findOrFail($request->id_gestion);
        $admitidos = $this->reporteService->aprobadosPorGestion($request->id_gestion);

        return view('admin.reportes.resultado', [
            'titulo' => "Admitidos - {$gestion->nombreCompleto}",
            'tipo' => 'aprobados_gestion',
            'datos' => $admitidos,
            'gestion' => $gestion,
        ]);
    }

    public function rendimientoPorGrupo(Request $request)
    {
        $request->validate(['id_grupo' => 'required|exists:grupo,id_grupo']);

        $grupo = Grupo::with('turno')->findOrFail($request->id_grupo);
        $datos = $this->reporteService->rendimientoPorGrupo($request->id_grupo);

        return view('admin.reportes.resultado', [
            'titulo' => "Rendimiento - Grupo {$grupo->nombre}",
            'tipo' => 'rendimiento_grupo',
            'datos' => $datos,
            'grupo' => $grupo,
        ]);
    }

    public function docenteDestacado(Request $request)
    {
        $request->validate(['id_gestion' => 'required|exists:gestion,id_gestion']);

        $gestion = Gestion::findOrFail($request->id_gestion);
        $ranking = $this->reporteService->docenteConMayorAprobacion($request->id_gestion);

        return view('admin.reportes.resultado', [
            'titulo' => "Ranking Docentes - {$gestion->nombreCompleto}",
            'tipo' => 'docente_destacado',
            'datos' => $ranking,
            'gestion' => $gestion,
        ]);
    }

    public function comparativaGestiones(Request $request)
    {
        $request->validate(['gestiones' => 'required|array|min:2']);

        $datos = $this->admisionService->comparativaGestiones($request->gestiones);

        return view('admin.reportes.resultado', [
            'titulo' => 'Comparativa entre Gestiones',
            'tipo' => 'comparativa',
            'datos' => $datos,
        ]);
    }

    public function admitidosPorCarrera(Request $request)
    {
        $request->validate(['id_gestion' => 'required|exists:gestion,id_gestion']);

        $gestion = Gestion::findOrFail($request->id_gestion);
        $datos = $this->reporteService->admitidosPorCarrera($request->id_gestion);

        return view('admin.reportes.resultado', [
            'titulo' => "Admitidos por Carrera - {$gestion->nombreCompleto}",
            'tipo' => 'por_carrera',
            'datos' => $datos,
            'gestion' => $gestion,
        ]);
    }

    public function exportarPdf(Request $request)
    {
        $request->validate([
            'tipo' => 'required|string',
            'id_gestion' => 'nullable|exists:gestion,id_gestion',
            'id_grupo' => 'nullable|exists:grupo,id_grupo',
        ]);

        $datos = $this->obtenerDatosReporte($request);
        return $this->reporteService->exportarPdf($request->tipo, $datos, "reporte_{$request->tipo}");
    }

    public function exportarCsv(Request $request)
    {
        $request->validate([
            'tipo' => 'required|string',
            'id_gestion' => 'nullable|exists:gestion,id_gestion',
        ]);

        $datos = $this->obtenerDatosParaCsv($request);
        return $this->reporteService->exportarCsv($datos['cabeceras'], $datos['filas'], "reporte_{$request->tipo}");
    }

    private function obtenerDatosReporte(Request $request): array
    {
        return match ($request->tipo) {
            'aprobados_gestion' => [
                'admitidos' => $this->reporteService->aprobadosPorGestion($request->id_gestion),
                'gestion' => Gestion::find($request->id_gestion),
            ],
            'rendimiento_grupo' => $this->reporteService->rendimientoPorGrupo($request->id_grupo),
            'docente_destacado' => [
                'ranking' => $this->reporteService->docenteConMayorAprobacion($request->id_gestion),
                'gestion' => Gestion::find($request->id_gestion),
            ],
            default => [],
        };
    }

    private function obtenerDatosParaCsv(Request $request): array
    {
        if ($request->tipo === 'aprobados_gestion') {
            $admitidos = $this->reporteService->aprobadosPorGestion($request->id_gestion);
            return [
                'cabeceras' => ['Nombre', 'Apellidos', 'CI', 'Carrera Admitida', 'Nota Final', 'Opción'],
                'filas' => $admitidos->map(fn($a) => [
                    $a->postulante->usuario->nombre,
                    $a->postulante->usuario->apellidos,
                    $a->postulante->usuario->ci,
                    $a->carrera->nombre,
                    $a->nota_final_cup,
                    $a->opcion_ingreso,
                ])->toArray(),
            ];
        }

        return ['cabeceras' => [], 'filas' => []];
    }
}
