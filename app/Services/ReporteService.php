<?php

namespace App\Services;

use App\Models\AdmisionFinal;
use App\Models\DocenteGrupo;
use App\Models\Gestion;
use App\Models\Grupo;
use App\Models\Nota;
use App\Models\Postulante;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReporteService
{
    /**
     * Admitidos de una gestión con detalle de carrera y opción de ingreso.
     */
    public function aprobadosPorGestion(int $gestionId): Collection
    {
        return AdmisionFinal::with(['postulante.usuario', 'carrera'])
            ->whereHas('postulante', fn($q) => $q->where('id_gestion', $gestionId))
            ->orderByDesc('nota_final_cup')
            ->get();
    }

    /**
     * Rendimiento de un grupo específico: lista de postulantes con notas.
     */
    public function rendimientoPorGrupo(int $grupoId): array
    {
        $postulantes = Postulante::with(['usuario', 'notas.materia'])
            ->where('id_grupo', $grupoId)
            ->get();

        $aprobados = $postulantes->filter(fn($p) => $p->aproboTodasLasMaterias())->count();
        $total = $postulantes->count();

        return [
            'postulantes' => $postulantes,
            'total' => $total,
            'aprobados' => $aprobados,
            'reprobados' => $total - $aprobados,
            'porcentaje_aprobacion' => $total > 0 ? round(($aprobados / $total) * 100, 1) : 0,
        ];
    }

    /**
     * Identifica al docente con mayor porcentaje de aprobados en sus grupos.
     */
    public function docenteConMayorAprobacion(int $gestionId): array
    {
        $asignaciones = DocenteGrupo::with(['docente.usuario', 'grupo.postulantes.notas', 'materia'])
            ->whereHas('grupo.postulantes', fn($q) => $q->where('id_gestion', $gestionId))
            ->get();

        $rendimientoPorDocente = $asignaciones->groupBy('id_docente')->map(function ($grupos, $docenteId) {
            $docente = $grupos->first()->docente;
            $todosPostulantes = $grupos->flatMap(fn($dg) => $dg->grupo->postulantes)->unique('id_postulante');
            $total = $todosPostulantes->count();
            $aprobados = $todosPostulantes->filter(fn($p) => $p->aproboTodasLasMaterias())->count();

            return [
                'docente' => $docente,
                'total_estudiantes' => $total,
                'aprobados' => $aprobados,
                'porcentaje' => $total > 0 ? round(($aprobados / $total) * 100, 1) : 0,
            ];
        });

        return $rendimientoPorDocente->sortByDesc('porcentaje')->values()->all();
    }

    /**
     * Admitidos por carrera con estadísticas de cupo.
     */
    public function admitidosPorCarrera(int $gestionId): Collection
    {
        return AdmisionFinal::with('carrera')
            ->whereHas('postulante', fn($q) => $q->where('id_gestion', $gestionId))
            ->get()
            ->groupBy('id_carrera_admitida')
            ->map(function ($admitidos) {
                $carrera = $admitidos->first()->carrera;
                return [
                    'carrera' => $carrera->nombre,
                    'cupo_maximo' => $carrera->cupo_maximo,
                    'admitidos' => $admitidos->count(),
                    'primera_opcion' => $admitidos->where('opcion_ingreso', 'PRIMERA OPCION')->count(),
                    'segunda_opcion' => $admitidos->where('opcion_ingreso', 'SEGUNDA OPCION')->count(),
                ];
            });
    }

    public function exportarPdf(string $tipo, array $datos, string $nombreArchivo): \Illuminate\Http\Response
    {
        if (!class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            abort(503, 'DomPDF no está instalado. Ejecute: composer require barryvdh/laravel-dompdf');
        }

        $datos['titulo'] = $datos['titulo'] ?? 'Reporte CUP';
        $pdf = Pdf::loadView('pdf.reporte', $datos);
        return $pdf->download("{$nombreArchivo}.pdf");
    }

    public function exportarCsv(array $cabeceras, array $filas, string $nombreArchivo): StreamedResponse
    {
        return response()->streamDownload(function () use ($cabeceras, $filas) {
            $handle = fopen('php://output', 'w');
            // BOM para compatibilidad con Excel y caracteres UTF-8
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));
            fputcsv($handle, $cabeceras, ';');

            foreach ($filas as $fila) {
                fputcsv($handle, $fila, ';');
            }

            fclose($handle);
        }, "{$nombreArchivo}.csv", [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
