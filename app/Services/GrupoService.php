<?php

namespace App\Services;

use App\Models\Grupo;
use App\Models\Postulante;
use App\Exceptions\CupoAgotadoException;

class GrupoService
{
    /**
     * Calcula cuántos grupos se necesitan para una cantidad de postulantes.
     * Ejemplo: 1000 estudiantes / 70 por grupo = 15 grupos (redondeado hacia arriba).
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
     * El trigger de PostgreSQL valida cupo y requisitos automáticamente.
     */
    public function asignarPostulanteAGrupo(Postulante $postulante, int $grupoId): void
    {
        $grupo = Grupo::findOrFail($grupoId);

        if (!$grupo->tieneDisponibilidad()) {
            throw new CupoAgotadoException(
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
        $grupo = Grupo::with(['postulantes.notas', 'docenteGrupos.docente.usuario', 'docenteGrupos.materia'])
            ->findOrFail($grupoId);

        $totalInscritos = $grupo->postulantes->count();
        $aprobados = $grupo->postulantes->filter(fn($p) => $p->aproboTodasLasMaterias())->count();

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
