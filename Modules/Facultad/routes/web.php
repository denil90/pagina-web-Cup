<?php

use Illuminate\Support\Facades\Route;
use Modules\Facultad\Http\Controllers\DocenteController;
use Modules\Facultad\Http\Controllers\RegisterDocenteController;
use Modules\Facultad\Http\Controllers\DocenteDashboardController;
use Modules\Facultad\Http\Controllers\DocenteRequisitoController;

// Rutas Públicas (Registro)
Route::middleware('web')->group(function () {
    Route::get('/registro-docente', [RegisterDocenteController::class, 'showRegistro'])->name('registro.docente');
    Route::post('/registro-docente', [RegisterDocenteController::class, 'registrar']);
});

// Rutas del Docente Postulante (Dashboard y Documentos)
Route::middleware(['web', 'auth', 'verificar.rol:docente'])
    ->prefix('docente')
    ->name('docente.')
    ->group(function () {
        Route::get('/dashboard', [DocenteDashboardController::class, 'index'])->name('dashboard');
        Route::get('/requisitos', [DocenteRequisitoController::class, 'index'])->name('requisitos');
        Route::post('/requisitos/titulo', [DocenteRequisitoController::class, 'subirTitulo'])->name('requisitos.titulo');
        Route::post('/requisitos/maestria', [DocenteRequisitoController::class, 'subirMaestria'])->name('requisitos.maestria');
        Route::post('/requisitos/diplomado', [DocenteRequisitoController::class, 'subirDiplomado'])->name('requisitos.diplomado');

        // Calificaciones por Docente
        Route::get('/grupos/{grupo}/materias/{materia}/estudiantes', [DocenteDashboardController::class, 'verEstudiantes'])->name('grupos.materia.estudiantes');
        Route::get('/estudiantes/{postulante}/materias/{materia}/nota', [DocenteDashboardController::class, 'registrarNota'])->name('estudiantes.materia.nota');
        Route::post('/estudiantes/nota', [DocenteDashboardController::class, 'guardarNota'])->name('estudiantes.materia.nota.store');
    });

// Rutas Administrativas
Route::middleware(['web', 'auth', 'verificar.rol:administrador,docente'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::resource('docentes', DocenteController::class)->except(['show', 'destroy']);
        Route::get('/docentes/{id}/detalle', [DocenteController::class, 'show'])->name('docentes.show');
        Route::put('/docentes/{id}/aprobar', [DocenteController::class, 'aprobar'])->name('docentes.aprobar');
        Route::put('/docentes/{id}/rechazar', [DocenteController::class, 'rechazar'])->name('docentes.rechazar');
        
        Route::get('/docentes/{id}/asignar', [DocenteController::class, 'showAsignar'])->name('docentes.asignar');
        Route::post('/docentes/{id}/asignar', [DocenteController::class, 'asignar'])->name('docentes.asignar.store');
        Route::delete('/docentes/asignacion/{id}', [DocenteController::class, 'removerAsignacion'])->name('docentes.asignacion.destroy');
    });
