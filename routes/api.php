<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AsistenciaController;

// Ruta a la que llamará React
Route::post('/marcar-asistencia', [AsistenciaController::class, 'marcar']);