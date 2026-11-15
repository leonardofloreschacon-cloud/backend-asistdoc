<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AsistenciaController;

Route::post('/marcar-asistencia', [AsistenciaController::class, 'marcar']);
Route::get('/asistencias-hoy', [AsistenciaController::class, 'listar']); // <- Agrega esta línea