<?php

namespace Modules\Academico\Domain\UseCases;

use Modules\Academico\Models\Carrera;

final class DeleteCarreraUseCase
{
    /**
     * Elimina una carrera si no tiene postulantes asociados.
     * La FK en PostgreSQL protege de borrado si hay referencias.
     *
     * @throws \RuntimeException Si no se puede eliminar (FK constraint)
     */
    public function execute(int $carreraId): void
    {
        $carrera = Carrera::findOrFail($carreraId);
        $carrera->delete();
    }
}
