<?php

namespace Tests\Unit;

use App\Models\Pago;
use Tests\TestCase;

class PagoTest extends TestCase
{
    public function test_esta_completado()
    {
        $pago = new Pago(['estado' => 'COMPLETADO']);
        $this->assertTrue($pago->estaCompletado());
    }

    public function test_no_esta_completado_cuando_pendiente()
    {
        $pago = new Pago(['estado' => 'PENDIENTE']);
        $this->assertFalse($pago->estaCompletado());
    }

    public function test_esta_pendiente()
    {
        $pago = new Pago(['estado' => 'PENDIENTE']);
        $this->assertTrue($pago->estaPendiente());
    }
}
