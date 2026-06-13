<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Modules\Facultad\Models\DocenteGrupo;
use Illuminate\Support\Facades\DB;

try {
    DB::beginTransaction();

    // Obtener todos los docentes que tienen alguna asignación
    $docentesConAsignaciones = DocenteGrupo::select('id_docente')
        ->groupBy('id_docente')
        ->havingRaw('COUNT(*) > 4')
        ->pluck('id_docente');

    $totalBorrados = 0;
    $docentesAfectados = 0;

    foreach ($docentesConAsignaciones as $id_docente) {
        // Obtener todas las asignaciones del docente (ordenadas por id para borrar las más recientes o las últimas)
        $asignaciones = DocenteGrupo::where('id_docente', $id_docente)
            ->orderBy('id', 'asc')
            ->get();

        if ($asignaciones->count() > 4) {
            $docentesAfectados++;
            $excedente = $asignaciones->count() - 4;
            
            // Tomamos los que sobran al final de la lista
            $paraBorrar = $asignaciones->slice(4);
            
            foreach ($paraBorrar as $asig) {
                $asig->delete();
                $totalBorrados++;
            }
            
            echo "Docente ID $id_docente: Tenía " . $asignaciones->count() . " materias. Se borraron $excedente.\n";
        }
    }

    DB::commit();

    if ($totalBorrados > 0) {
        echo "Limpieza completada exitosamente.\n";
        echo "Se corrigieron $docentesAfectados docentes y se eliminaron $totalBorrados asignaciones excedentes en total.\n";
    } else {
        echo "No se encontraron docentes con más de 4 materias. La base de datos ya está limpia.\n";
    }

} catch (\Exception $e) {
    DB::rollBack();
    echo "Error durante la limpieza: " . $e->getMessage() . "\n";
}
