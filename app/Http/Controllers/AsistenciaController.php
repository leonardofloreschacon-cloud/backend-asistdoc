<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AsistenciaController extends Controller
{
    public function marcar(Request $request)
    {
        $request->validate(['dni' => 'required|digits:8']);
        $dni = $request->dni;

        $docente = DB::table('docentes')->where('dni', $dni)->first();

        if (!$docente) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Docente no encontrado en el sistema.'
            ], 404);
        }

        $ahora = Carbon::now('America/Lima');
        $fecha = $ahora->toDateString();
        $hora = $ahora->toTimeString();

        // Buscar si ya existe un registro hoy
        $asistenciaHoy = DB::table('asistencias')
            ->where('docente_id', $docente->id)
            ->where('fecha', $fecha)
            ->first();

        // CASO 1: Primer marcaje del día -> ENTRADA
        if (!$asistenciaHoy) {
            $estado = ($hora > '07:45:00') ? 'Tardanza' : 'Asistió';

            DB::table('asistencias')->insert([
                'docente_id' => $docente->id,
                'fecha' => $fecha,
                'hora_marca' => $hora,
                'hora_salida' => null,
                'estado' => $estado,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'tipo' => 'entrada',
                'mensaje' => 'Entrada registrada con éxito.',
                'docente' => $docente->nombres . ' ' . $docente->apellidos,
                'hora' => $hora,
                'hora_salida' => null,
                'estado' => $estado
            ]);
        }

        // CASO 2: Segundo marcaje del día -> SALIDA
        if (is_null($asistenciaHoy->hora_salida)) {
            DB::table('asistencias')
                ->where('id', $asistenciaHoy->id)
                ->update([
                    'hora_salida' => $hora,
                    'updated_at' => now(),
                ]);

            return response()->json([
                'success' => true,
                'tipo' => 'salida',
                'mensaje' => 'Hora de salida registrada con éxito.',
                'docente' => $docente->nombres . ' ' . $docente->apellidos,
                'hora' => $asistenciaHoy->hora_marca,
                'hora_salida' => $hora,
                'estado' => $asistenciaHoy->estado
            ]);
        }

        // CASO 3: Ya marcó entrada y salida hoy
        return response()->json([
            'success' => false,
            'mensaje' => 'Ya registraste tu entrada y tu salida el día de hoy.'
        ], 400);
    }

    public function listar()
    {
        $fecha = Carbon::now('America/Lima')->toDateString();

        $asistencias = DB::table('asistencias')
            ->join('docentes', 'asistencias.docente_id', '=', 'docentes.id')
            ->where('asistencias.fecha', $fecha)
            ->select(
                'asistencias.id',
                'docentes.nombres',
                'docentes.apellidos',
                'asistencias.fecha',
                'asistencias.hora_marca',
                'asistencias.hora_salida',
                'asistencias.estado'
            )
            ->orderBy('asistencias.hora_marca', 'desc')
            ->get();

        $data = $asistencias->map(function ($a) {
            return [
                'id' => $a->id,
                'docente' => $a->nombres . ' ' . $a->apellidos,
                'fecha' => $a->fecha,
                'hora' => $a->hora_marca,
                'hora_salida' => $a->hora_salida,
                'estado' => $a->estado
            ];
        });

        return response()->json($data);
    }

    // Nuevo: historial exclusivo del docente autenticado
    public function misAsistencias(Request $request)
    {
        $docente = $request->user()->docente;

        if (!$docente) {
            return response()->json(['message' => 'No tienes un perfil de docente asociado.'], 404);
        }

        $asistencias = DB::table('asistencias')
            ->where('docente_id', $docente->id)
            ->orderBy('fecha', 'desc')
            ->orderBy('hora_marca', 'desc')
            ->get();

        return response()->json($asistencias);
    }
}