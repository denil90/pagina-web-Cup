<?php

namespace Database\Seeders;

use App\Models\Gestion;
use Illuminate\Database\Seeder;

class GestionSeeder extends Seeder
{
    /**
     * Crea 3 gestiones según el requerimiento:
     * Gestión 1-2025, Gestión 2-2025, Gestión 1-2026
     */
    public function run(): void
    {
        $gestiones = [
            ['semestre' => '1', 'anio' => 2025],
            ['semestre' => '2', 'anio' => 2025],
            ['semestre' => '1', 'anio' => 2026],
        ];

        foreach ($gestiones as $gestion) {
            Gestion::create($gestion);
        }
    }
}
