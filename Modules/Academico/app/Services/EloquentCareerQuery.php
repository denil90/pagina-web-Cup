<?php

namespace Modules\Academico\Services;

use Modules\Academico\Contracts\CareerQueryInterface;
use Modules\Academico\Models\Carrera;

/**
 * Implementación Eloquent del contrato CareerQueryInterface.
 * Otros módulos inyectan la interfaz, no esta clase concreta.
 */
class EloquentCareerQuery implements CareerQueryInterface
{
    public function exists(int $careerId): bool
    {
        return Carrera::where('id', $careerId)->exists();
    }

    public function getMaxQuota(int $careerId): int
    {
        return Carrera::findOrFail($careerId)->cupo_maximo;
    }

    public function findById(int $careerId): ?array
    {
        $carrera = Carrera::find($careerId);
        return $carrera ? $carrera->toArray() : null;
    }

    public function all(): array
    {
        return Carrera::all()->toArray();
    }
}
