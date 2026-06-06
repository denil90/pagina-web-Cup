<?php

namespace Modules\Academico\Services;

use Modules\Academico\Contracts\SubjectQueryInterface;
use Modules\Academico\Models\Materia;

/**
 * Implementación Eloquent del contrato SubjectQueryInterface.
 */
class EloquentSubjectQuery implements SubjectQueryInterface
{
    public function exists(int $subjectId): bool
    {
        return Materia::where('id_materia', $subjectId)->exists();
    }

    public function findById(int $subjectId): ?array
    {
        $materia = Materia::find($subjectId);
        return $materia ? $materia->toArray() : null;
    }

    public function all(): array
    {
        return Materia::all()->toArray();
    }
}
