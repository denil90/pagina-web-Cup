<?php

namespace Modules\Academico\Contracts;

/**
 * Contrato para que otros módulos consulten el periodo/gestión académica activa.
 * Implementado dentro de Academico, consumido por Admision.
 */
interface PeriodQueryInterface
{
    public function getCurrentPeriodId(): ?int;

    /** @return array{id_gestion: int, semestre: string, anio: int}|null */
    public function findById(int $periodId): ?array;

    /** @return array<int, array{id_gestion: int, semestre: string, anio: int}> */
    public function all(): array;
}
