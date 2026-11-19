<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AsistenciaController extends Controller
{
    public function marcar(Request $request)
    {
        // 1. Validar que llegue el DNI de 8 dígitos
        $request->validate(['dni' => 'required|digits:8']);
        $dni = $request->dni;

        // 2. Buscar al docente en la base de datos
        $docente = DB::table('docentes')->where('dni', $dni)->first();

        if (!$docente) {
            return response()->json([
                'success' => false, 
                'mensaje' => 'Docente no encontrado en el sistema.'
            ], 404);
        }

        // 3. Obtener la hora EXACTA del servidor (RN-06)
        $ahora = Carbon::now('America/Lima');
        $fecha = $ahora->toDateString(); // Ej: 2026-11-16
        $hora = $ahora->toTimeString();  // Ej: 08:15:00

        // 4. Verificar duplicidad (RN-01) - ¡NUEVO!
        $yaMarco = DB::table('asistencias')
            ->where('docente_id', $docente->id)
            ->where('fecha', $fecha)
            ->exists();

        if ($yaMarco) {
            return response()->json([
                'success' => false,
                'mensaje' => 'La asistencia ya ha sido registrada el día de hoy.'
            ], 400); // 400 Bad Request
        }

        // 5. Lógica de Tardanza (RN-02)
        // Nota: Por ahora está estático (7:45 AM). En una fase futura, se puede cruzar con la tabla 'horarios'.
        $estado = ($hora > '07:45:00') ? 'Tardanza' : 'Asistió';

        // 6. Registrar en la tabla asistencias
        DB::table('asistencias')->insert([
            'docente_id' => $docente->id,
            'fecha' => $fecha,
            'hora_marca' => $hora,
            'estado' => $estado
        ]);

        return response()->json([
            'success' => true,
            'mensaje' => 'Marcaje registrado con éxito',
            'docente' => $docente->nombres . ' ' . $docente->apellidos,
            'hora' => $hora,
            'estado' => $estado
        ]);
        
    }

    public function listar()
    {
        $ahora = Carbon::now('America/Lima');
        $fecha = $ahora->toDateString();

        $asistencias = DB::table('asistencias')
            ->join('docentes', 'asistencias.docente_id', '=', 'docentes.id')
            ->where('asistencias.fecha', $fecha)
            ->select('docentes.nombres', 'docentes.apellidos', 'asistencias.hora_marca', 'asistencias.estado')
            ->orderBy('asistencias.hora_marca', 'desc')
            ->get();

        // Formateamos los datos para que React los entienda igual
        $data = $asistencias->map(function($a) {
            return [
                'docente' => $a->nombres . ' ' . $a->apellidos,
                'hora' => $a->hora_marca,
                'estado' => $a->estado
            ];
        });

        return response()->json($data);
    }
}