<?php

use Illuminate\Support\Facades\Route;
use Modules\Academico\Http\Controllers\CarreraController;
use Modules\Academico\Http\Controllers\GestionController;
use Modules\Academico\Http\Controllers\MateriaController;

/*
|--------------------------------------------------------------------------
| Módulo Academico — Rutas Web
|--------------------------------------------------------------------------
| CU07: Gestionar Periodos / Gestiones Académicas
| CU08: Gestionar Carreras
| CU09: Gestionar Materias
|
| Todas las rutas requieren autenticación + rol administrador/docente.
| Los nombres de ruta se mantienen iguales (admin.gestiones.*, admin.carreras.*, etc.)
| para compatibilidad con el código existente.
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verificar.rol:administrador,docente'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        // CU07: Gestiones
        Route::resource('gestiones', GestionController::class)->except('show');

        // CU08: Carreras
        Route::resource('carreras', CarreraController::class)->except('show');

        // CU09: Materias
        Route::resource('materias', MateriaController::class)->except('show');
    });
