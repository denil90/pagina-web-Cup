<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Postulante;
use Illuminate\Support\Facades\Route;

// =============================================
// RUTAS PÚBLICAS
// =============================================
Route::get('/', fn() => redirect()->route('login'));

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/registro', [RegisterController::class, 'showRegistroPostulante'])->name('registro');
Route::post('/registro', [RegisterController::class, 'registrarPostulante']);

// Consulta pública de resultados de admisión (sin login)
Route::get('/resultados-admision', [Postulante\ResultadoController::class, 'consultaPublica'])
    ->name('resultados.publicos');

// =============================================
// RUTAS DEL POSTULANTE (requiere login + rol postulante)
// =============================================
Route::middleware(['auth', 'verificar.rol:postulante'])->prefix('postulante')->name('postulante.')->group(function () {
    Route::get('/dashboard', [Postulante\PostulanteDashboardController::class, 'index'])->name('dashboard');

    // Pago
    Route::get('/pago', [Postulante\PagoController::class, 'index'])->name('pago');
    Route::post('/pago/crear', [Postulante\PagoController::class, 'crearPago'])->name('pago.crear');
    Route::post('/pago/confirmar', [Postulante\PagoController::class, 'confirmar'])->name('pago.confirmar');
    Route::post('/pago/simular', [Postulante\PagoController::class, 'simularPago'])->name('pago.simular');

    // Notas y resultados
    Route::get('/notas', [Postulante\ResultadoController::class, 'misNotas'])->name('notas');
    Route::get('/resultados', [Postulante\ResultadoController::class, 'misResultados'])->name('resultados');
});

// =============================================
// RUTAS DEL ADMINISTRADOR (requiere login + rol administrador o docente)
// =============================================
Route::middleware(['auth', 'verificar.rol:administrador,docente'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Gestiones
    Route::resource('gestiones', Admin\GestionController::class)->except('show');

    // Carreras
    Route::resource('carreras', Admin\CarreraController::class)->except('show');

    // Materias
    Route::resource('materias', Admin\MateriaController::class)->except('show');

    // Grupos
    Route::resource('grupos', Admin\GrupoController::class);

    // Docentes
    Route::resource('docentes', Admin\DocenteController::class)->except(['show', 'destroy']);
    Route::get('/docentes/{id}/asignar', [Admin\DocenteController::class, 'showAsignar'])->name('docentes.asignar');
    Route::post('/docentes/{id}/asignar', [Admin\DocenteController::class, 'asignar'])->name('docentes.asignar.store');
    Route::delete('/docentes/asignacion/{id}', [Admin\DocenteController::class, 'removerAsignacion'])->name('docentes.asignacion.destroy');

    // Postulantes
    Route::get('/postulantes', [Admin\PostulanteAdminController::class, 'index'])->name('postulantes.index');
    Route::get('/postulantes/{id}', [Admin\PostulanteAdminController::class, 'show'])->name('postulantes.show');
    Route::put('/postulantes/{id}/requisitos', [Admin\PostulanteAdminController::class, 'verificarRequisitos'])->name('postulantes.requisitos');
    Route::put('/postulantes/{id}/grupo', [Admin\PostulanteAdminController::class, 'asignarGrupo'])->name('postulantes.grupo');

    // Notas
    Route::get('/notas', [Admin\NotaController::class, 'index'])->name('notas.index');
    Route::get('/notas/{postulante}/{materia}', [Admin\NotaController::class, 'registrar'])->name('notas.registrar');
    Route::post('/notas', [Admin\NotaController::class, 'guardar'])->name('notas.guardar');

    // Admisión
    Route::get('/admision', [Admin\AdmisionController::class, 'index'])->name('admision.index');
    Route::post('/admision/procesar', [Admin\AdmisionController::class, 'procesar'])->name('admision.procesar');
    Route::get('/admision/resultados/{gestion}', [Admin\AdmisionController::class, 'resultados'])->name('admision.resultados');

    // Reportes
    Route::get('/reportes', [Admin\ReporteController::class, 'index'])->name('reportes.index');
    Route::post('/reportes/aprobados', [Admin\ReporteController::class, 'aprobadosPorGestion'])->name('reportes.aprobados');
    Route::post('/reportes/rendimiento', [Admin\ReporteController::class, 'rendimientoPorGrupo'])->name('reportes.rendimiento');
    Route::post('/reportes/docente', [Admin\ReporteController::class, 'docenteDestacado'])->name('reportes.docente');
    Route::post('/reportes/comparativa', [Admin\ReporteController::class, 'comparativaGestiones'])->name('reportes.comparativa');
    Route::post('/reportes/carreras', [Admin\ReporteController::class, 'admitidosPorCarrera'])->name('reportes.carreras');
    Route::post('/reportes/exportar/pdf', [Admin\ReporteController::class, 'exportarPdf'])->name('reportes.exportar.pdf');
    Route::post('/reportes/exportar/csv', [Admin\ReporteController::class, 'exportarCsv'])->name('reportes.exportar.csv');

    // Configuración (Aulas, Horarios, Turnos)
    Route::get('/aulas', [Admin\ConfiguracionController::class, 'aulasIndex'])->name('aulas.index');
    Route::post('/aulas', [Admin\ConfiguracionController::class, 'aulasStore'])->name('aulas.store');
    Route::delete('/aulas/{id}', [Admin\ConfiguracionController::class, 'aulasDestroy'])->name('aulas.destroy');

    Route::get('/horarios', [Admin\ConfiguracionController::class, 'horariosIndex'])->name('horarios.index');
    Route::post('/horarios', [Admin\ConfiguracionController::class, 'horariosStore'])->name('horarios.store');
    Route::delete('/horarios/{id}', [Admin\ConfiguracionController::class, 'horariosDestroy'])->name('horarios.destroy');

    Route::get('/turnos', [Admin\ConfiguracionController::class, 'turnosIndex'])->name('turnos.index');
    Route::post('/turnos', [Admin\ConfiguracionController::class, 'turnosStore'])->name('turnos.store');
    Route::delete('/turnos/{id}', [Admin\ConfiguracionController::class, 'turnosDestroy'])->name('turnos.destroy');
});
