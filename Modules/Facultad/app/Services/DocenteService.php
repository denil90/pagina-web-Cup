<?php

namespace Modules\Facultad\Services;

use Modules\Facultad\Models\DocenteGrupo;
use Modules\Facultad\Models\Docente;
use Modules\Planificacion\Models\Grupo;

class DocenteService
{
    private const MAX_MATERIAS_POR_DOCENTE = 4;

    /**
     * Asigna un docente a un grupo para una materia.
     */
    public function asignarAGrupo(int $docenteId, int $grupoId, int $materiaId): DocenteGrupo
    {
        $this->validarCargaHoraria($docenteId);
        $this->validarSinCruceHorarios($docenteId, $grupoId);
        $this->validarSinDuplicado($docenteId, $grupoId, $materiaId);

        return DocenteGrupo::create([
            'id_docente' => $docenteId,
            'id_grupo' => $grupoId,
            'id_materia' => $materiaId,
        ]);
    }

    public function removerDeGrupo(int $asignacionId): void
    {
        DocenteGrupo::findOrFail($asignacionId)->delete();
    }

    private function validarCargaHoraria(int $docenteId): void
    {
        $cantidadMaterias = DocenteGrupo::where('id_docente', $docenteId)->count();

        if ($cantidadMaterias >= self::MAX_MATERIAS_POR_DOCENTE) {
            throw new \RuntimeException(
                "El docente ya alcanzó el límite máximo de " . self::MAX_MATERIAS_POR_DOCENTE . " materias asignadas."
            );
        }
    }

    private function validarSinCruceHorarios(int $docenteId, int $grupoId): void
    {
        $grupoNuevo = Grupo::findOrFail($grupoId);

        if (!$grupoNuevo->id_horario) {
            return;
        }

        $tieneCruce = DocenteGrupo::where('id_docente', $docenteId)
            ->whereHas('grupo', function ($query) use ($grupoNuevo) {
                $query->where('id_horario', $grupoNuevo->id_horario);
            })
            ->exists();

        if ($tieneCruce) {
            throw new \RuntimeException(
                "Cruce de horarios detectado. El docente ya tiene asignaciones en ese horario."
            );
        }
    }

    private function validarSinDuplicado(int $docenteId, int $grupoId, int $materiaId): void
    {
        $existe = DocenteGrupo::where('id_docente', $docenteId)
            ->where('id_grupo', $grupoId)
            ->where('id_materia', $materiaId)
            ->exists();

        if ($existe) {
            throw new \RuntimeException(
                "El docente ya está asignado a esta materia en este grupo."
            );
        }
    }

    public function obtenerCargaDocente(int $docenteId): array
    {
        $asignaciones = DocenteGrupo::with(['grupo.horario', 'grupo.turno', 'materia'])
            ->where('id_docente', $docenteId)
            ->get();

        return [
            'total_grupos' => $asignaciones->unique('id_grupo')->count(),
            'asignaciones' => $asignaciones,
        ];
    }
}
