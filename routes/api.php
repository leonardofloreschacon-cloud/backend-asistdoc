<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AsistenciaController;
use App\Http\Controllers\DocenteController;
use App\Http\Controllers\JustificacionController;

// === RUTAS PÚBLICAS ===
Route::post('/login', [AuthController::class, 'login']);
Route::post('/asistencia/marcar', [AsistenciaController::class, 'marcar']);
Route::get('/asistencias-hoy', [AsistenciaController::class, 'listar']);

// === RUTAS PROTEGIDAS (Sanctum) ===
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // Rutas Administrador
    Route::get('/docentes', [DocenteController::class, 'index']);
    Route::post('/docentes', [DocenteController::class, 'store']);
    Route::get('/justificaciones', [JustificacionController::class, 'index']);
    Route::patch('/justificaciones/{id}', [JustificacionController::class, 'actualizarEstado']);

    // Rutas Docente
    Route::get('/mis-asistencias', [AsistenciaController::class, 'misAsistencias']);
    Route::get('/mis-justificaciones', [JustificacionController::class, 'misJustificaciones']);
    Route::post('/justificaciones', [JustificacionController::class, 'store']);
    Route::get('/asistencias/reporte', [AsistenciaController::class, 'reporte']);
});