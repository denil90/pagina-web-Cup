<?php

namespace Database\Seeders;

use App\Models\Administrador;
use App\Models\Usuario;
use Illuminate\Database\Seeder;

class AdministradorSeeder extends Seeder
{
    /**
     * Crea un usuario administrador por defecto.
     * Credenciales: admin@cup.fic.edu.bo / admin123
     */
    public function run(): void
    {
        $usuario = Usuario::create([
            'nombre' => 'Admin',
            'apellidos' => 'Sistema CUP',
            'ci' => '0000001',
            'contrasena' => 'admin123',
            'fechanac' => '1990-01-01',
            'sexo' => 'M',
            'direccion' => 'Facultad de Informática y Computación',
            'telefono' => '70000001',
            'rol' => 'administrador',
            'correo' => 'admin@cup.fic.edu.bo',
        ]);

        Administrador::create([
            'id_admin' => $usuario->id_usuario,
        ]);
    }
}
