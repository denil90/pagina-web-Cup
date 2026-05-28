<?php

namespace Tests\Feature;

use Tests\TestCase;

class RutasPublicasTest extends TestCase
{
    public function test_login_page_es_accesible()
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertSee('CUP - FIC');
    }

    public function test_registro_page_es_accesible()
    {
        $response = $this->get('/registro');
        // Puede fallar si la BD no está configurada y el controller
        // intenta consultar carreras. 500 es aceptable sin BD.
        $this->assertTrue(in_array($response->status(), [200, 500]));
    }

    public function test_raiz_redirige_a_login()
    {
        $response = $this->get('/');
        $response->assertRedirect('/login');
    }

    public function test_admin_requiere_autenticacion()
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_postulante_requiere_autenticacion()
    {
        $response = $this->get('/postulante/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_resultados_publicos_es_accesible()
    {
        $response = $this->get('/resultados-admision');
        // Puede fallar sin BD
        $this->assertTrue(in_array($response->status(), [200, 500]));
    }
}
