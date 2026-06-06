<?php

namespace Modules\Academico\Domain\UseCases;

use Modules\Academico\Domain\DTOs\CarreraDTO;
use Modules\Academico\Models\Carrera;

final class CreateCarreraUseCase
{
    /**
     * Crea una nueva carrera en el catálogo académico.
     *
     * @throws \InvalidArgumentException Si el cupo es inválido
     * @throws \RuntimeException Si ya existe una carrera con ese nombre
     */
    public function execute(CarreraDTO $dto): Carrera
    {
        if ($dto->cupoMaximo <= 0) {
            throw new \InvalidArgumentException(
                'El cupo máximo debe ser mayor a 0.'
            );
        }

        if (Carrera::where('nombre', $dto->nombre)->exists()) {
            throw new \RuntimeException(
                "Ya existe una carrera con el nombre '{$dto->nombre}'."
            );
        }

        return Carrera::create([
            'nombre'      => $dto->nombre,
            'descripcion' => $dto->descripcion,
            'cupo_maximo' => $dto->cupoMaximo,
        ]);
    }
}
