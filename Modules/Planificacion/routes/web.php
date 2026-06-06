<?php

use Illuminate\Support\Facades\Route;
use Modules\Planificacion\Http\Controllers\GrupoController;
use Modules\Planificacion\Http\Controllers\AulaController;
use Modules\Planificacion\Http\Controllers\HorarioController;
use Modules\Planificacion\Http\Controllers\TurnoController;

Route::middleware(['auth', 'verificar.rol:administrador,docente'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // Grupos
        Route::resource('grupos', GrupoController::class);

        // Aulas
        Route::get('/aulas', [AulaController::class, 'index'])->name('aulas.index');
        Route::post('/aulas', [AulaController::class, 'store'])->name('aulas.store');
        Route::put('/aulas/{id}', [AulaController::class, 'update'])->name('aulas.update');
        Route::delete('/aulas/{id}', [AulaController::class, 'destroy'])->name('aulas.destroy');

        // Horarios
        Route::get('/horarios', [HorarioController::class, 'index'])->name('horarios.index');
        Route::post('/horarios', [HorarioController::class, 'store'])->name('horarios.store');
        Route::put('/horarios/{id}', [HorarioController::class, 'update'])->name('horarios.update');
        Route::delete('/horarios/{id}', [HorarioController::class, 'destroy'])->name('horarios.destroy');

        // Turnos
        Route::get('/turnos', [TurnoController::class, 'index'])->name('turnos.index');
        Route::post('/turnos', [TurnoController::class, 'store'])->name('turnos.store');
        Route::delete('/turnos/{id}', [TurnoController::class, 'destroy'])->name('turnos.destroy');
    });
