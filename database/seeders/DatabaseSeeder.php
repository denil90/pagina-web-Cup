<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Orden de ejecución importante: las tablas con dependencias van después.
     * NOTA: Ejecutar SOLO después de crear las tablas en PostgreSQL
     * con el script database/sql/database.sql
     */
    public function run(): void
    {
        $this->call([
            CarreraSeeder::class,
            MateriaSeeder::class,
            GestionSeeder::class,
            InfraestructuraSeeder::class,
            AdministradorSeeder::class,
        ]);
    }
}
