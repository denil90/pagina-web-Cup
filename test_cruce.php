<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\Facultad\Models\DocenteGrupo;
use Modules\Planificacion\Models\Grupo;

$asignaciones = DocenteGrupo::with('grupo')->get();
$c = 0;
foreach($asignaciones as $a) {
    echo "Docente: {$a->id_docente}, Grupo: {$a->id_grupo} ({$a->grupo->nombre}), Horario: {$a->grupo->id_horario}\n";
    $c++;
    if ($c > 10) break;
}
