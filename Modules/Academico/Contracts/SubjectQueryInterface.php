<?php

namespace Modules\Academico\Contracts;

/**
 * Contrato para que otros módulos consulten información de materias.
 * Implementado dentro de Academico, consumido por Evaluacion y Facultad.
 */
interface SubjectQueryInterface
{
    public function exists(int $subjectId): bool;

    /** @return array{id_materia: int, nombre: string, porcentaje_examen1: ?float, porcentaje_examen2: ?float, porcentaje_examen3: ?float}|null */
    public function findById(int $subjectId): ?array;

    /** @return array<int, array{id_materia: int, nombre: string}> */
    public function all(): array;
}
