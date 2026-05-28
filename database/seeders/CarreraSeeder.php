<?php

namespace Database\Seeders;

use App\Models\Carrera;
use Illuminate\Database\Seeder;

class CarreraSeeder extends Seeder
{
    public function run(): void
    {
        $carreras = [
            [
                'nombre' => 'Ingeniería en Sistemas',
                'descripcion' => 'Carrera orientada al desarrollo de software, bases de datos y sistemas de información.',
                'cupo_maximo' => 200,
            ],
            [
                'nombre' => 'Ingeniería Informática',
                'descripcion' => 'Carrera enfocada en hardware, redes, inteligencia artificial y computación aplicada.',
                'cupo_maximo' => 150,
            ],
            [
                'nombre' => 'Redes y Telecomunicaciones',
                'descripcion' => 'Carrera especializada en infraestructura de redes, seguridad y telecomunicaciones.',
                'cupo_maximo' => 100,
            ],
            [
                'nombre' => 'Ciencias de la Computación',
                'descripcion' => 'Carrera orientada a la investigación en algoritmos, IA, ciencia de datos y computación teórica.',
                'cupo_maximo' => 80,
            ],
        ];

        foreach ($carreras as $carrera) {
            Carrera::create($carrera);
        }
    }
}
