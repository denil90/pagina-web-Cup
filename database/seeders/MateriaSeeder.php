<?php

namespace Database\Seeders;

use App\Models\Materia;
use Illuminate\Database\Seeder;

class MateriaSeeder extends Seeder
{
    public function run(): void
    {
        // Porcentajes: Ex1 30%, Ex2 30%, ExFinal 40% (suman 100%)
        $materias = [
            ['nombre' => 'Computación', 'porcentaje_examen1' => 30, 'porcentaje_examen2' => 30, 'porcentaje_examen3' => 40],
            ['nombre' => 'Matemáticas', 'porcentaje_examen1' => 30, 'porcentaje_examen2' => 30, 'porcentaje_examen3' => 40],
            ['nombre' => 'Inglés',      'porcentaje_examen1' => 30, 'porcentaje_examen2' => 30, 'porcentaje_examen3' => 40],
            ['nombre' => 'Física',      'porcentaje_examen1' => 30, 'porcentaje_examen2' => 30, 'porcentaje_examen3' => 40],
        ];

        foreach ($materias as $materia) {
            Materia::create($materia);
        }
    }
}
