<?php

namespace Tests\Unit;

use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UsuarioTest extends TestCase
{
    public function test_contrasena_se_hashea_automaticamente()
    {
        $usuario = new Usuario();
        $usuario->contrasena = 'miPassword123';

        // La contraseña no debe ser texto plano
        $this->assertNotEquals('miPassword123', $usuario->contrasena);
        $this->assertTrue(Hash::check('miPassword123', $usuario->contrasena));
    }

    public function test_nombre_completo_combina_nombre_y_apellidos()
    {
        $usuario = new Usuario([
            'nombre' => 'Juan',
            'apellidos' => 'Pérez López',
        ]);

        $this->assertEquals('Juan Pérez López', $usuario->nombreCompleto);
    }

    public function test_es_administrador()
    {
        $usuario = new Usuario(['rol' => 'administrador']);
        $this->assertTrue($usuario->esAdministrador());
        $this->assertFalse($usuario->esDocente());
        $this->assertFalse($usuario->esPostulante());
    }

    public function test_es_postulante()
    {
        $usuario = new Usuario(['rol' => 'postulante']);
        $this->assertTrue($usuario->esPostulante());
        $this->assertFalse($usuario->esAdministrador());
    }

    public function test_es_docente()
    {
        $usuario = new Usuario(['rol' => 'docente']);
        $this->assertTrue($usuario->esDocente());
        $this->assertFalse($usuario->esPostulante());
    }
}
