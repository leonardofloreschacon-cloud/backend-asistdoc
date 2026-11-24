<?php

namespace App\Http\Controllers;

use App\Models\Docente;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class DocenteController extends Controller
{
    public function index()
    {
        return response()->json(Docente::with('usuario')->get());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'dni' => 'required|string|size:8|unique:docentes',
            'nombres' => 'required|string',
            'apellidos' => 'required|string',
            'pin' => 'required|string|min:4',
            'foto_url' => 'nullable|string|max:255',
        ]);

        // Crear el usuario asociado
        $user = User::create([
            'username' => $validated['dni'],
            'password' => Hash::make($validated['pin']),
            'rol_id' => 2, // 2 = Rol Docente
        ]);

        // Crear el registro del docente
        $docente = Docente::create([
            'dni' => $validated['dni'],
            'nombres' => $validated['nombres'],
            'apellidos' => $validated['apellidos'],
            'foto_url' => $validated['foto_url'] ?? null,
            'usuario_id' => $user->id,
        ]);

        return response()->json($docente, 201);
    }
}