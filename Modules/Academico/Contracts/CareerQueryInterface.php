<?php

namespace Modules\Academico\Contracts;

/**
 * Contrato para que otros módulos consulten información de carreras.
 * Implementado dentro de Academico, consumido por Admision y Evaluacion.
 */
interface CareerQueryInterface
{
    public function exists(int $careerId): bool;

    public function getMaxQuota(int $careerId): int;

    /** @return array{id: int, nombre: string, descripcion: ?string, cupo_maximo: int}|null */
    public function findById(int $careerId): ?array;

    /** @return array<int, array{id: int, nombre: string, descripcion: ?string, cupo_maximo: int}> */
    public function all(): array;
}
