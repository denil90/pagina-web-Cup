<?php

namespace Modules\Planificacion\Services;

use Modules\Planificacion\Models\Grupo;
use Illuminate\Support\Facades\DB;

class GrupoService
{
    /**
     * Calcula cuántos grupos se necesitan para una cantidad de postulantes.
     */
    public function calcularGruposNecesarios(int $totalPostulantes, int $capacidadPorGrupo = 70): int
    {
        if ($capacidadPorGrupo <= 0) {
            throw new \InvalidArgumentException('La capacidad por grupo debe ser mayor a 0.');
        }

        return (int) ceil($totalPostulantes / $capacidadPorGrupo);
    }

    public function verificarDisponibilidad(int $grupoId): bool
    {
        $grupo = Grupo::findOrFail($grupoId);
        return $grupo->tieneDisponibilidad();
    }

    /**
     * Asigna un postulante al primer grupo disponible.
     */
    public function asignarPostulanteAGrupo($postulante, int $grupoId): void
    {
        $grupo = Grupo::findOrFail($grupoId);

        if (!$grupo->tieneDisponibilidad()) {
            throw new \Modules\Planificacion\Exceptions\CupoAgotadoException(
                "El grupo '{$grupo->nombre}' ya alcanzó su capacidad máxima de {$grupo->capacidad_maxima} estudiantes."
            );
        }

        $postulante->id_grupo = $grupoId;
        $postulante->save();
    }

    public function obtenerGruposConDisponibilidad()
    {
        return Grupo::with(['horario', 'aula', 'turno'])
            ->get()
            ->filter(fn(Grupo $grupo) => $grupo->tieneDisponibilidad());
    }

    public function obtenerEstadisticasGrupo(int $grupoId): array
    {
        $grupo = Grupo::with(['docenteGrupos.docente.usuario', 'docenteGrupos.materia'])
            ->findOrFail($grupoId);

        $totalInscritos = DB::table('postulante')
            ->where('id_grupo', $grupoId)
            ->count();

        $aprobados = 0;
        if ($totalInscritos > 0) {
            // Obtener IDs de postulantes en este grupo
            $postulanteIds = DB::table('postulante')
                ->where('id_grupo', $grupoId)
                ->pluck('id_postulante');

            // Postulantes con al menos una nota registrada
            $conNotasIds = DB::table('notas')
                ->whereIn('id_postulante', $postulanteIds)
                ->distinct()
                ->pluck('id_postulante');

            // Postulantes con alguna nota reprobada (promedio < 60)
            $reprobadosIds = DB::table('notas')
                ->whereIn('id_postulante', $postulanteIds)
                ->where('promedio', '<', 60)
                ->distinct()
                ->pluck('id_postulante');

            // Aprobados: con notas y sin ninguna reprobada
            $aprobados = $conNotasIds->diff($reprobadosIds)->count();
        }

        return [
            'grupo' => $grupo,
            'total_inscritos' => $totalInscritos,
            'aprobados' => $aprobados,
            'reprobados' => $totalInscritos - $aprobados,
            'porcentaje_aprobacion' => $totalInscritos > 0
                ? round(($aprobados / $totalInscritos) * 100, 1)
                : 0,
        ];
    }
}
