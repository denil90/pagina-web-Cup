<?php

namespace Modules\Academico\Domain\UseCases;

use Modules\Academico\Domain\DTOs\CarreraDTO;
use Modules\Academico\Models\Carrera;

final class UpdateCarreraUseCase
{
    /**
     * Actualiza una carrera existente.
     *
     * @throws \RuntimeException Si la carrera no existe
     * @throws \RuntimeException Si el nuevo nombre duplica otra carrera
     */
    public function execute(int $carreraId, CarreraDTO $dto): Carrera
    {
        $carrera = Carrera::findOrFail($carreraId);

        // Verificar unicidad de nombre (excluyendo la misma carrera)
        $duplicada = Carrera::where('nombre', $dto->nombre)
            ->where('id', '!=', $carreraId)
            ->exists();

        if ($duplicada) {
            throw new \RuntimeException(
                "Ya existe otra carrera con el nombre '{$dto->nombre}'."
            );
        }

        $carrera->update([
            'nombre'      => $dto->nombre,
            'descripcion' => $dto->descripcion,
            'cupo_maximo' => $dto->cupoMaximo,
        ]);

        return $carrera->fresh();
    }
}
