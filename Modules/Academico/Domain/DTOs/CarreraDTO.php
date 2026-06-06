<?php

namespace Modules\Academico\Domain\DTOs;

/**
 * DTO para crear/actualizar una carrera.
 * Agnóstico a Laravel: solo tipos primitivos PHP.
 */
final readonly class CarreraDTO
{
    public function __construct(
        public string  $nombre,
        public ?string $descripcion,
        public int     $cupoMaximo,
    ) {}
}
