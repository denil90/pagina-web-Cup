<?php

namespace Modules\Academico\Services;

use Modules\Academico\Contracts\PeriodQueryInterface;
use Modules\Academico\Models\Gestion;

/**
 * Implementación Eloquent del contrato PeriodQueryInterface.
 */
class EloquentPeriodQuery implements PeriodQueryInterface
{
    public function getCurrentPeriodId(): ?int
    {
        return Gestion::max('id_gestion');
    }

    public function findById(int $periodId): ?array
    {
        $gestion = Gestion::find($periodId);
        return $gestion ? $gestion->toArray() : null;
    }

    public function all(): array
    {
        return Gestion::orderByDesc('anio')
            ->orderByDesc('semestre')
            ->get()
            ->toArray();
    }
}
