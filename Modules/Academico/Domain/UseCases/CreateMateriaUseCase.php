<?php

namespace Modules\Academico\Domain\UseCases;

use Modules\Academico\Domain\DTOs\MateriaDTO;
use Modules\Academico\Models\Materia;

final class CreateMateriaUseCase
{
    /**
     * Crea una nueva materia validando que los porcentajes sumen 100%.
     *
     * @throws \InvalidArgumentException Si los porcentajes no suman 100%
     */
    public function execute(MateriaDTO $dto): Materia
    {
        $total = $dto->porcentajeExamen1 + $dto->porcentajeExamen2 + $dto->porcentajeExamen3;

        if (abs($total - 100) > 0.01) {
            throw new \InvalidArgumentException(
                'Los porcentajes de los 3 exámenes deben sumar exactamente 100%. Suma actual: ' . round($total, 2) . '%.'
            );
        }

        return Materia::create([
            'nombre'             => $dto->nombre,
            'porcentaje_examen1' => $dto->porcentajeExamen1,
            'porcentaje_examen2' => $dto->porcentajeExamen2,
            'porcentaje_examen3' => $dto->porcentajeExamen3,
        ]);
    }
}
