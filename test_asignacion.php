<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\Facultad\Services\DocenteService;
use Modules\Planificacion\Models\Grupo;

$service = new DocenteService();
try {
    // Intentar asignar otra materia al docente 9 en el grupo N3 (id_grupo=133)
    $service->asignarAGrupo(9, 133, 5); // 5 es otra materia
    echo "Asignado exitosamente\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
