<?php

namespace Tests\Unit;

use App\Models\AdmisionFinal;
use Tests\TestCase;

class AdmisionFinalTest extends TestCase
{
    public function test_fue_admitido_en_primera_opcion()
    {
        $admision = new AdmisionFinal([
            'opcion_ingreso' => 'PRIMERA OPCION',
        ]);

        $this->assertTrue($admision->fueAdmitidoEnPrimeraOpcion());
    }

    public function test_no_fue_admitido_en_primera_opcion()
    {
        $admision = new AdmisionFinal([
            'opcion_ingreso' => 'SEGUNDA OPCION',
        ]);

        $this->assertFalse($admision->fueAdmitidoEnPrimeraOpcion());
    }
}
