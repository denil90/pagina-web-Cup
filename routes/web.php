<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use Illuminate\Support\Facades\Route;

// =============================================
// RUTAS PÚBLICAS
// =============================================
Route::get('/', fn() => redirect()->route('bienvenida'));

// Módulo Seguridad (login, logout) gestionado por Modules/Seguridad/routes/web.php


// Módulo Admision (registro, dashboard, pago, requisitos, admin postulantes) gestionado por Modules/Admision/routes/web.php


// =============================================
// RUTAS DEL POSTULANTE (requiere login + rol postulante)
// =============================================
Route::middleware(['auth', 'verificar.rol:postulante'])->prefix('postulante')->name('postulante.')->group(function () {
    //
});

// =============================================
// RUTAS DEL ADMINISTRADOR (requiere login + rol administrador o docente)
// =============================================
Route::middleware(['auth', 'verificar.rol:administrador,docente'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Módulo Academico (gestiones, carreras, materias) gestionado por Modules/Academico/routes/web.php

    // Módulo Planificacion (grupos, aulas, horarios, turnos) gestionado por Modules/Planificacion/routes/web.php


    // Módulo Facultad (docentes, asignaciones) gestionado por Modules/Facultad/routes/web.php


});

