<?php

namespace Tests\Unit;

use App\Models\Postulante;
use App\Models\Nota;
use Tests\TestCase;

class PostulanteTest extends TestCase
{
    public function test_cumple_requisitos_devuelve_true_cuando_ambos_documentos_presentes()
    {
        $postulante = new Postulante([
            'titulo_bachiller' => true,
            'libreta_de_ultimo_anio' => true,
        ]);

        $this->assertTrue($postulante->cumpleRequisitos());
    }

    public function test_cumple_requisitos_devuelve_false_cuando_falta_documento()
    {
        $postulante = new Postulante([
            'titulo_bachiller' => true,
            'libreta_de_ultimo_anio' => false,
        ]);

        $this->assertFalse($postulante->cumpleRequisitos());
    }

    public function test_cumple_requisitos_devuelve_false_cuando_ninguno_presente()
    {
        $postulante = new Postulante([
            'titulo_bachiller' => false,
            'libreta_de_ultimo_anio' => false,
        ]);

        $this->assertFalse($postulante->cumpleRequisitos());
    }
}
