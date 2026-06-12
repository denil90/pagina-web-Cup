<?php

use Illuminate\Support\Facades\Route;
use Modules\Seguridad\Http\Controllers\LoginController;

Route::middleware('web')->group(function () {
    Route::get('/bienvenida', [LoginController::class, 'bienvenida'])->name('bienvenida');
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
