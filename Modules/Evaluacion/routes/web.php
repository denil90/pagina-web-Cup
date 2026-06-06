<?php

use Illuminate\Support\Facades\Route;
use Modules\Evaluacion\Http\Controllers\ResultadoController;
use Modules\Evaluacion\Http\Controllers\NotaController;
use Modules\Evaluacion\Http\Controllers\AdmisionController;
use Modules\Evaluacion\Http\Controllers\ReporteController;

Route::middleware('web')->group(function () {
    // Consulta pública de resultados de admisión (sin login)
    Route::get('/resultados-admision', [ResultadoController::class, 'consultaPublica'])->name('resultados.publicos');

    // Rutas del postulante (requiere login + rol postulante)
    Route::middleware(['auth', 'verificar.rol:postulante'])
        ->prefix('postulante')
        ->name('postulante.')
        ->group(function () {
            Route::get('/notas', [ResultadoController::class, 'misNotas'])->name('notas');
            Route::get('/resultados', [ResultadoController::class, 'misResultados'])->name('resultados');
        });

    // Rutas del administrador (requiere login + rol administrador o docente)
    Route::middleware(['auth', 'verificar.rol:administrador,docente'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            // Notas
            Route::get('/notas', [NotaController::class, 'index'])->name('notas.index');
            Route::get('/notas/{postulante}/{materia}', [NotaController::class, 'registrar'])->name('notas.registrar');
            Route::post('/notas', [NotaController::class, 'guardar'])->name('notas.guardar');

            // Admisión
            Route::get('/admision', [AdmisionController::class, 'index'])->name('admision.index');
            Route::post('/admision/procesar', [AdmisionController::class, 'procesar'])->name('admision.procesar');
            Route::get('/admision/resultados/{gestion}', [AdmisionController::class, 'resultados'])->name('admision.resultados');

            // Reportes
            Route::get('/reportes', [ReporteController::class, 'index'])->name('reportes.index');
            Route::post('/reportes/aprobados', [ReporteController::class, 'aprobadosPorGestion'])->name('reportes.aprobados');
            Route::post('/reportes/rendimiento', [ReporteController::class, 'rendimientoPorGrupo'])->name('reportes.rendimiento');
            Route::post('/reportes/docente', [ReporteController::class, 'docenteDestacado'])->name('reportes.docente');
            Route::post('/reportes/comparativa', [ReporteController::class, 'comparativaGestiones'])->name('reportes.comparativa');
            Route::post('/reportes/carreras', [ReporteController::class, 'admitidosPorCarrera'])->name('reportes.carreras');
            Route::post('/reportes/exportar/pdf', [ReporteController::class, 'exportarPdf'])->name('reportes.exportar.pdf');
            Route::post('/reportes/exportar/csv', [ReporteController::class, 'exportarCsv'])->name('reportes.exportar.csv');
        });
});
