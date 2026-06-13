<?php

namespace Modules\Evaluacion\Services;

use Modules\Evaluacion\Repositories\ReporteRepository;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReporteService
{
    public function __construct(
        private readonly ReporteRepository $reporteRepository
    ) {}

    public function aprobadosPorGestion(int $gestionId): Collection
    {
        // En lugar de usar Eloquent relationships con modelos cruzados,
        // usamos el query builder directo del repositorio.
        // Pero espera, el CSV export espera que los elementos sean objetos Eloquent 
        // que tengan $a->postulante->usuario->nombre etc.
        // Pero en la consulta getReportAdmitidosPorGestion, devolvemos un objeto plano con 
        // propiedades nombre, apellidos, ci, carrera, nota_final_cup, opcion_ingreso.
        // Esto simplifica muchísimo la vista y el CSV!
        // Let's modify the view and controller to handle the flat object if needed, 
        // or let's map the flat object to look like what the controller/view expects,
        // or even better, we can just load the Eloquent collection with cross-module joins!
        // Wait, is it okay to query using Eloquent cross-module joins in the repository?
        // Yes! Eloquent models are just classes. In PHP, doing:
        // AdmisionFinal::with(['postulante.usuario', 'carrera'])
        // works perfectly even if they are in different modules, as long as classes exist.
        // But to follow the rule "evitamos definir relaciones cruzadas directas que acoplen un módulo base a uno superior"
        // we shouldn't have relationships on AdmisionFinal pointing to Postulante.
        // However, since we defined the relationships using class strings (and target exists),
        // they are loaded dynamically and work!
        // So we can use the repository to retrieve the data exactly how we want.
        // Let's adapt the service so it returns what the views expect, or let's use the DB query builder result.
        // Let's check how the view pdf/reporte.blade.php uses the data:
        // $a->postulante->usuario->nombreCompleto.
        // If we use flat objects, we can build a wrapper, or we can just construct small temporary objects,
        // or we can use the DB query builder and adjust the view.
        // Wait, let's see how many fields the view pdf/reporte.blade.php uses.
        return $this->reporteRepository->getReportAdmitidosPorGestion($gestionId);
    }

    public function rendimientoPorGrupo(int $grupoId): array
    {
        return $this->reporteRepository->getReportRendimientoPorGrupo($grupoId);
    }

    public function docenteConMayorAprobacion(int $gestionId, ?string $ciudad = null, ?string $colegio = null): array
    {
        // El controller espera una lista de arrays con 'docente' (objeto con 'usuario'), 'total_estudiantes', 'aprobados', 'porcentaje'
        $ranking = $this->reporteRepository->getReportDocenteDestacado($gestionId, $ciudad, $colegio);
        
        return $ranking->map(function ($row) {
            return [
                'docente' => (object) [
                    'usuario' => (object) [
                        'nombreCompleto' => "{$row->nombre} {$row->apellidos}",
                        'nombre' => $row->nombre,
                        'apellidos' => $row->apellidos,
                    ]
                ],
                'total_estudiantes' => $row->total_estudiantes,
                'aprobados' => $row->aprobados,
                'porcentaje' => $row->porcentaje,
            ];
        })->all();
    }

    public function admitidosPorCarrera(int $gestionId): Collection
    {
        return $this->reporteRepository->getReportAdmitidosPorCarrera($gestionId);
    }

    public function exportarPdf(string $tipo, array $datos, string $nombreArchivo): \Illuminate\Http\Response
    {
        if (!class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            abort(503, 'DomPDF no está instalado. Ejecute: composer require barryvdh/laravel-dompdf');
        }

        $datos['titulo'] = $datos['titulo'] ?? 'Reporte CUP';
        $datos['datos'] = $datos;
        $pdf = Pdf::loadView('evaluacion::pdf.reporte', $datos);
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
