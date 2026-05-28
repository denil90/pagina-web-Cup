<?php

namespace Tests\Unit;

use App\Models\Grupo;
use App\Models\Gestion;
use App\Models\Horario;
use App\Models\Aula;
use App\Models\Carrera;
use App\Services\PagoService;
use Tests\TestCase;

class ModelosTest extends TestCase
{
    public function test_gestion_nombre_completo()
    {
        $gestion = new Gestion(['semestre' => '2', 'anio' => 2026]);
        $this->assertEquals('Gestión 2 - 2026', $gestion->nombreCompleto);
    }

    public function test_horario_rango()
    {
        $horario = new Horario([
            'dia' => 'Lunes',
            'hora_inicio' => '07:00',
            'hora_final' => '09:00',
        ]);

        $this->assertEquals('Lunes 07:00 - 09:00', $horario->rango);
    }

    public function test_aula_descripcion()
    {
        $aula = new Aula([
            'nombre' => 'Aula 101',
            'edificio' => 'A',
            'capacidad' => 70,
        ]);

        $this->assertEquals('Aula 101 - Edificio A (Cap: 70)', $aula->descripcion);
    }

    public function test_pago_service_monto_inscripcion_es_700()
    {
        $service = new PagoService();
        $this->assertEquals(700.00, $service->obtenerMontoInscripcion());
        $this->assertEquals('BOB', $service->obtenerMoneda());
    }

    public function test_pago_service_modo_test_por_defecto()
    {
        $service = new PagoService();
        $this->assertTrue($service->estaEnModoTest());
    }
}
