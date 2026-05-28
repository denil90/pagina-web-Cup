<?php

namespace Database\Seeders;

use App\Models\Turno;
use App\Models\Aula;
use App\Models\Horario;
use Illuminate\Database\Seeder;

class InfraestructuraSeeder extends Seeder
{
    public function run(): void
    {
        // Turnos: Mañana, Tarde, Noche
        $turnos = ['Mañana', 'Tarde', 'Noche'];
        foreach ($turnos as $turno) {
            Turno::create(['nombre' => $turno]);
        }

        // Aulas con capacidad de 70 (requisito del CUP)
        $aulas = [
            ['nombre' => 'Aula 101', 'edificio' => 'Edificio A', 'capacidad' => 70],
            ['nombre' => 'Aula 102', 'edificio' => 'Edificio A', 'capacidad' => 70],
            ['nombre' => 'Aula 201', 'edificio' => 'Edificio A', 'capacidad' => 70],
            ['nombre' => 'Aula 202', 'edificio' => 'Edificio A', 'capacidad' => 70],
            ['nombre' => 'Aula 301', 'edificio' => 'Edificio B', 'capacidad' => 70],
            ['nombre' => 'Aula 302', 'edificio' => 'Edificio B', 'capacidad' => 70],
            ['nombre' => 'Aula 401', 'edificio' => 'Edificio B', 'capacidad' => 70],
            ['nombre' => 'Lab. Comp. 1', 'edificio' => 'Edificio C', 'capacidad' => 40],
            ['nombre' => 'Lab. Comp. 2', 'edificio' => 'Edificio C', 'capacidad' => 40],
            ['nombre' => 'Aula Magna', 'edificio' => 'Edificio Central', 'capacidad' => 70],
            ['nombre' => 'Aula Virtual', 'edificio' => 'En Línea', 'capacidad' => 70],
        ];

        foreach ($aulas as $aula) {
            Aula::create($aula);
        }

        // Horarios distribuidos por turno
        $horarios = [
            // Mañana
            ['dia' => 'Lunes',     'hora_inicio' => '07:00', 'hora_final' => '09:00'],
            ['dia' => 'Lunes',     'hora_inicio' => '09:15', 'hora_final' => '11:15'],
            ['dia' => 'Martes',    'hora_inicio' => '07:00', 'hora_final' => '09:00'],
            ['dia' => 'Martes',    'hora_inicio' => '09:15', 'hora_final' => '11:15'],
            ['dia' => 'Miércoles', 'hora_inicio' => '07:00', 'hora_final' => '09:00'],
            ['dia' => 'Miércoles', 'hora_inicio' => '09:15', 'hora_final' => '11:15'],
            ['dia' => 'Jueves',    'hora_inicio' => '07:00', 'hora_final' => '09:00'],
            ['dia' => 'Jueves',    'hora_inicio' => '09:15', 'hora_final' => '11:15'],
            ['dia' => 'Viernes',   'hora_inicio' => '07:00', 'hora_final' => '09:00'],
            ['dia' => 'Viernes',   'hora_inicio' => '09:15', 'hora_final' => '11:15'],
            // Tarde
            ['dia' => 'Lunes',     'hora_inicio' => '14:00', 'hora_final' => '16:00'],
            ['dia' => 'Lunes',     'hora_inicio' => '16:15', 'hora_final' => '18:15'],
            ['dia' => 'Martes',    'hora_inicio' => '14:00', 'hora_final' => '16:00'],
            ['dia' => 'Miércoles', 'hora_inicio' => '14:00', 'hora_final' => '16:00'],
            ['dia' => 'Jueves',    'hora_inicio' => '14:00', 'hora_final' => '16:00'],
            ['dia' => 'Viernes',   'hora_inicio' => '14:00', 'hora_final' => '16:00'],
            // Noche
            ['dia' => 'Lunes',     'hora_inicio' => '18:30', 'hora_final' => '20:30'],
            ['dia' => 'Martes',    'hora_inicio' => '18:30', 'hora_final' => '20:30'],
            ['dia' => 'Miércoles', 'hora_inicio' => '18:30', 'hora_final' => '20:30'],
            ['dia' => 'Jueves',    'hora_inicio' => '18:30', 'hora_final' => '20:30'],
        ];

        foreach ($horarios as $horario) {
            Horario::create($horario);
        }
    }
}
