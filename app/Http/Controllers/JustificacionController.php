<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JustificacionController extends Controller
{
    // Docente envía su justificación (con archivo opcional)
    public function store(Request $request)
    {
        $validated = $request->validate([
            'fecha_falta' => 'required|date',
            'motivo' => 'required|string|min:5|max:500',
            'archivo' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $docente = $request->user()->docente;

        if (!$docente) {
            return response()->json(['message' => 'Perfil de docente no encontrado.'], 404);
        }

        $archivoNombre = null;
        $archivoData = null;

        if ($request->hasFile('archivo')) {
            $file = $request->file('archivo');
            $archivoNombre = $file->getClientOriginalName();
            $mime = $file->getMimeType();
            $contenido = file_get_contents($file->getRealPath());
            $archivoData = 'data:' . $mime . ';base64,' . base64_encode($contenido);
        }

        $id = DB::table('justificaciones')->insertGetId([
            'docente_id' => $docente->id,
            'fecha_falta' => $validated['fecha_falta'],
            'motivo' => $validated['motivo'],
            'archivo_nombre' => $archivoNombre,
            'archivo_data' => $archivoData,
            'estado' => 'Pendiente',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'message' => 'Justificación enviada correctamente.',
            'id' => $id
        ], 201);
    }

    // Docente ve sus propias justificaciones
    public function misJustificaciones(Request $request)
    {
        $docente = $request->user()->docente;

        if (!$docente) {
            return response()->json([], 200);
        }

        $lista = DB::table('justificaciones')
            ->where('docente_id', $docente->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($lista);
    }

    // Administrador ve todas las justificaciones
    public function index()
    {
        $lista = DB::table('justificaciones')
            ->join('docentes', 'justificaciones.docente_id', '=', 'docentes.id')
            ->select(
                'justificaciones.id',
                'docentes.dni',
                'docentes.nombres',
                'docentes.apellidos',
                'justificaciones.fecha_falta',
                'justificaciones.motivo',
                'justificaciones.archivo_nombre',
                'justificaciones.archivo_data',
                'justificaciones.estado',
                'justificaciones.created_at'
            )
            ->orderBy('justificaciones.created_at', 'desc')
            ->get();

        return response()->json($lista);
    }

    // Administrador aprueba o rechaza
    public function actualizarEstado(Request $request, $id)
    {
        $validated = $request->validate([
            'estado' => 'required|in:Aprobada,Rechazada',
        ]);

        DB::table('justificaciones')
            ->where('id', $id)
            ->update([
                'estado' => $validated['estado'],
                'updated_at' => now(),
            ]);

        return response()->json(['message' => 'Estado actualizado correctamente.']);
    }
}