<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EstudiantesRollbackSeeder extends Seeder
{
    /**
     * Reverte la siembra de estudiantes de prueba.
     * Elimina únicamente los postulantes y usuarios cuyo correo termina en @postulante.cup.edu
     */
    public function run(): void
    {
        // 1. Obtener los IDs de los usuarios sembrados por EstudiantesSeeder
        $uIds = DB::table('usuario')
            ->where('correo', 'LIKE', '%@postulante.cup.edu')
            ->pluck('id_usuario')
            ->toArray();

        if (empty($uIds)) {
            $this->command->info("No se encontraron estudiantes de prueba para revertir.");
            return;
        }

        $this->command->info("Revirtiendo la siembra de " . count($uIds) . " estudiantes...");

        DB::transaction(function () use ($uIds) {
            // Eliminar registros asociados en orden de integridad referencial
            DB::table('admision_final')->whereIn('id_postulante', $uIds)->delete();
            DB::table('notas')->whereIn('id_postulante', $uIds)->delete();
            DB::table('pago')->whereIn('id_postulante', $uIds)->delete();
            DB::table('postulante')->whereIn('id_postulante', $uIds)->delete();
            DB::table('usuario')->whereIn('id_usuario', $uIds)->delete();
        });

        $this->command->info("Siembra revertida exitosamente. Todos los datos de prueba fueron eliminados de forma segura.");
    }
}
