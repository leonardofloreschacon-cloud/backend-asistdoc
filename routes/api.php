<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\DocenteController;

// === RUTAS PÚBLICAS ===
Route::post('/login', [AuthController::class, 'login']);
Route::post('/asistencia/marcar', [AsistenciaController::class, 'marcar']);

// ¡NUEVA RUTA AGREGADA PARA CARGAR EL HISTORIAL!
Route::get('/asistencias-hoy', [AsistenciaController::class, 'listar']);

// === RUTAS PROTEGIDAS (Solo Administrador) ===
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/docentes', [DocenteController::class, 'index']);
    Route::post('/docentes', [DocenteController::class, 'store']);
});