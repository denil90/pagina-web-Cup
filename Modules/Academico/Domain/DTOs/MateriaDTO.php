<?php

namespace Modules\Academico\Domain\DTOs;

/**
 * DTO para crear/actualizar una materia.
 * Agnóstico a Laravel: solo tipos primitivos PHP.
 */
final readonly class MateriaDTO
{
    public function __construct(
        public string $nombre,
        public float  $porcentajeExamen1,
        public float  $porcentajeExamen2,
        public float  $porcentajeExamen3,
    ) {}
}
