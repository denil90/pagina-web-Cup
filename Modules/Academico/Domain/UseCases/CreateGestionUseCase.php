<?php

namespace Modules\Academico\Domain\UseCases;

use Modules\Academico\Domain\DTOs\GestionDTO;
use Modules\Academico\Models\Gestion;

final class CreateGestionUseCase
{
    /**
     * Crea un nuevo periodo/gestión académica.
     *
     * @throws \RuntimeException Si ya existe esa combinación semestre+año
     */
    public function execute(GestionDTO $dto): Gestion
    {
        $existe = Gestion::where('semestre', $dto->semestre)
            ->where('anio', $dto->anio)
            ->exists();

        if ($existe) {
            throw new \RuntimeException(
                "Ya existe la gestión {$dto->semestre} - {$dto->anio}."
            );
        }

        return Gestion::create([
            'semestre' => $dto->semestre,
            'anio'     => $dto->anio,
        ]);
    }
}
