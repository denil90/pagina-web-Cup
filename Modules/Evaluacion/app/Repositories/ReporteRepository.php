<?php

namespace Modules\Evaluacion\Repositories;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Repositorio de reportes con acceso directo a tablas de múltiples módulos.
 *
 * CONVENCIÓN:
 * - Todos los métodos públicos llevan prefijo "getReport"
 * - Se usan query builders de Eloquent (no raw SQL puro)
 * - Las tablas se referencian explícitamente (no models de otros módulos)
 * - Este repositorio es la ÚNICA excepción a la regla de no-cross-module-access
 */
final class ReporteRepository
{
    /**
     * Reporte: Admitidos por gestión con datos completos.
     * Cruza: admision_final ← postulante ← usuario + carrera
     */
    public function getReportAdmitidosPorGestion(int $gestionId): Collection
    {
        return DB::table('admision_final as af')
            ->join('postulante as p', 'af.id_postulante', '=', 'p.id_postulante')
            ->join('usuario as u', 'p.id_postulante', '=', 'u.id_usuario')
            ->join('carrera as c', 'af.id_carrera_admitida', '=', 'c.id')
            ->where('p.id_gestion', $gestionId)
            ->orderByDesc('af.nota_final_cup')
            ->select([
                'u.nombre',
                'u.apellidos',
                'u.ci',
                'c.nombre as carrera',
                'af.nota_final_cup',
                'af.opcion_ingreso',
            ])
            ->get();
    }

    /**
     * Reporte: Rendimiento por grupo.
     * Cruza: postulante ← usuario + notas + grupo
     */
    public function getReportRendimientoPorGrupo(int $grupoId): array
    {
        $postulantes = DB::table('postulante as p')
            ->join('usuario as u', 'p.id_postulante', '=', 'u.id_usuario')
            ->where('p.id_grupo', $grupoId)
            ->select([
                'p.id_postulante',
                'u.nombre',
                'u.apellidos',
                'u.ci',
            ])
            ->get();

        $notas = DB::table('notas')
            ->whereIn('id_postulante', $postulantes->pluck('id_postulante'))
            ->get()
            ->groupBy('id_postulante');

        $total = $postulantes->count();
        $aprobados = 0;

        $detalle = $postulantes->map(function ($p) use ($notas, &$aprobados) {
            $notasPostulante = $notas->get($p->id_postulante, collect());
            $aproboTodo = $notasPostulante->isNotEmpty()
                && $notasPostulante->every(fn($n) => $n->promedio >= 60);

            if ($aproboTodo) {
                $aprobados++;
            }

            return (object) [
                'nombre'    => "{$p->nombre} {$p->apellidos}",
                'ci'        => $p->ci,
                'promedio'  => round($notasPostulante->avg('promedio') ?? 0, 2),
                'estado'    => $aproboTodo ? 'APROBADO' : 'REPROBADO',
            ];
        });

        return [
            'postulantes'             => $detalle,
            'total'                   => $total,
            'aprobados'               => $aprobados,
            'reprobados'              => $total - $aprobados,
            'porcentaje_aprobacion'   => $total > 0 ? round(($aprobados / $total) * 100, 1) : 0,
        ];
    }

    /**
     * Reporte: Ranking de docentes por porcentaje de aprobados.
     * Cruza: docente_grupo ← docente ← usuario + grupo ← postulante ← notas
     */
    public function getReportDocenteDestacado(int $gestionId, ?string $ciudad = null, ?string $colegio = null): Collection
    {
        // Subquery: postulantes con todas las materias aprobadas
        $postulantesMaterias = DB::table('notas')
            ->select('id_postulante')
            ->selectRaw('COUNT(*) as total_materias')
            ->selectRaw("SUM(CASE WHEN estado = 'APROBADO' THEN 1 ELSE 0 END) as materias_aprobadas")
            ->groupBy('id_postulante');

        return DB::table('docente_grupo as dg')
            ->join('docente as d', 'dg.id_docente', '=', 'd.id_docente')
            ->join('usuario as u', 'd.id_docente', '=', 'u.id_usuario')
            ->join('grupo as g', 'dg.id_grupo', '=', 'g.id_grupo')
            ->join('postulante as p', 'p.id_grupo', '=', 'g.id_grupo')
            ->joinSub($postulantesMaterias, 'nm', 'nm.id_postulante', '=', 'p.id_postulante')
            ->where('p.id_gestion', $gestionId)
            ->when($ciudad, function($q) use ($ciudad) {
                $q->where('p.ciudad', $ciudad);
            })
            ->when($colegio, function($q) use ($colegio) {
                $q->where('p.colegio_procedencia', $colegio);
            })
            ->groupBy('dg.id_docente', 'u.nombre', 'u.apellidos')
            ->select([
                'u.nombre',
                'u.apellidos',
                DB::raw('COUNT(DISTINCT p.id_postulante) as total_estudiantes'),
                DB::raw('COUNT(DISTINCT CASE WHEN nm.total_materias = nm.materias_aprobadas THEN p.id_postulante END) as aprobados'),
                DB::raw('ROUND(
                    COUNT(DISTINCT CASE WHEN nm.total_materias = nm.materias_aprobadas THEN p.id_postulante END)::numeric
                    / NULLIF(COUNT(DISTINCT p.id_postulante), 0) * 100, 1
                ) as porcentaje'),
            ])
            ->orderByDesc('porcentaje')
            ->get();
    }

    /**
     * Reporte: Comparativa entre gestiones.
     * Cruza: gestion ← postulante + admision_final
     */
    public function getReportComparativaGestiones(array $gestionIds): Collection
    {
        return DB::table('gestion as g')
            ->whereIn('g.id_gestion', $gestionIds)
            ->leftJoin('postulante as p', 'p.id_gestion', '=', 'g.id_gestion')
            ->leftJoin('admision_final as af', 'af.id_postulante', '=', 'p.id_postulante')
            ->groupBy('g.id_gestion', 'g.semestre', 'g.anio')
            ->select([
                'g.id_gestion',
                DB::raw("'Gestión ' || g.semestre || ' - ' || g.anio as gestion"),
                DB::raw('COUNT(DISTINCT p.id_postulante) as postulantes'),
                DB::raw('COUNT(DISTINCT af.id_postulante) as admitidos'),
                DB::raw('COUNT(DISTINCT p.id_postulante) - COUNT(DISTINCT af.id_postulante) as no_admitidos'),
                DB::raw('ROUND(
                    COUNT(DISTINCT af.id_postulante)::numeric
                    / NULLIF(COUNT(DISTINCT p.id_postulante), 0) * 100, 1
                ) as tasa_admision'),
            ])
            ->orderBy('g.anio')
            ->orderBy('g.semestre')
            ->get();
    }

    /**
     * Reporte: Admitidos por carrera con estadísticas de cupo.
     * Cruza: admision_final ← carrera + postulante
     */
    public function getReportAdmitidosPorCarrera(int $gestionId): Collection
    {
        return DB::table('admision_final as af')
            ->join('carrera as c', 'af.id_carrera_admitida', '=', 'c.id')
            ->join('postulante as p', 'af.id_postulante', '=', 'p.id_postulante')
            ->where('p.id_gestion', $gestionId)
            ->groupBy('c.id', 'c.nombre', 'c.cupo_maximo')
            ->select([
                'c.nombre as carrera',
                'c.cupo_maximo',
                DB::raw('COUNT(*) as admitidos'),
                DB::raw("SUM(CASE WHEN af.opcion_ingreso = 'PRIMERA OPCION' THEN 1 ELSE 0 END) as primera_opcion"),
                DB::raw("SUM(CASE WHEN af.opcion_ingreso = 'SEGUNDA OPCION' THEN 1 ELSE 0 END) as segunda_opcion"),
            ])
            ->get();
    }
}
