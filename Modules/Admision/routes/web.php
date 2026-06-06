<?php

use Illuminate\Support\Facades\Route;
use Modules\Admision\Http\Controllers\RegisterController;
use Modules\Admision\Http\Controllers\PostulanteDashboardController;
use Modules\Admision\Http\Controllers\PagoController;
use Modules\Admision\Http\Controllers\RequisitoController;
use Modules\Admision\Http\Controllers\PostulanteAdminController;

Route::middleware('web')->group(function () {
    // Registro público de postulante
    Route::get('/registro', [RegisterController::class, 'showRegistroPostulante'])->name('registro');
    Route::post('/registro', [RegisterController::class, 'registrarPostulante']);

    // Rutas del postulante (requiere login + rol postulante)
    Route::middleware(['auth', 'verificar.rol:postulante'])
        ->prefix('postulante')
        ->name('postulante.')
        ->group(function () {
            Route::get('/dashboard', [PostulanteDashboardController::class, 'index'])->name('dashboard');

            // Pago
            Route::get('/pago', [PagoController::class, 'index'])->name('pago');
            Route::post('/pago/crear', [PagoController::class, 'crearPago'])->name('pago.crear');
            Route::post('/pago/confirmar', [PagoController::class, 'confirmar'])->name('pago.confirmar');
            Route::post('/pago/simular', [PagoController::class, 'simularPago'])->name('pago.simular');

            // Requisitos (subida de documentos)
            Route::get('/requisitos', [RequisitoController::class, 'index'])->name('requisitos');
            Route::post('/requisitos/titulo', [RequisitoController::class, 'subirTitulo'])->name('requisitos.titulo');
            Route::post('/requisitos/libreta', [RequisitoController::class, 'subirLibreta'])->name('requisitos.libreta');
        });

    // Rutas de administración para postulantes
    Route::middleware(['auth', 'verificar.rol:administrador,docente'])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('/postulantes', [PostulanteAdminController::class, 'index'])->name('postulantes.index');
            Route::get('/postulantes/{id}', [PostulanteAdminController::class, 'show'])->name('postulantes.show');
            Route::put('/postulantes/{id}/requisitos', [PostulanteAdminController::class, 'verificarRequisitos'])->name('postulantes.requisitos');
            Route::put('/postulantes/{id}/grupo', [PostulanteAdminController::class, 'asignarGrupo'])->name('postulantes.grupo');
        });
});
