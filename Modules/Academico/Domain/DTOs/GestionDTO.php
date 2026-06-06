<?php

namespace Modules\Academico\Domain\DTOs;

/**
 * DTO para crear/actualizar una gestión académica.
 * Agnóstico a Laravel: solo tipos primitivos PHP.
 */
final readonly class GestionDTO
{
    public function __construct(
        public string $semestre,
        public int    $anio,
    ) {}
}
