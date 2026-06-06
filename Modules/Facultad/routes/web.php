<?php

use Illuminate\Support\Facades\Route;
use Modules\Facultad\Http\Controllers\DocenteController;

Route::middleware(['auth', 'verificar.rol:administrador,docente'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('docentes', DocenteController::class)->except(['show', 'destroy']);
        Route::get('/docentes/{id}/asignar', [DocenteController::class, 'showAsignar'])->name('docentes.asignar');
        Route::post('/docentes/{id}/asignar', [DocenteController::class, 'asignar'])->name('docentes.asignar.store');
        Route::delete('/docentes/asignacion/{id}', [DocenteController::class, 'removerAsignacion'])->name('docentes.asignacion.destroy');
    });
