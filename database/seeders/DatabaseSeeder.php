<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Insertar los Roles
        DB::table('roles')->insert([
            ['nombre' => 'Admin'],
            ['nombre' => 'Docente'],
        ]);

        // 2. Crear el Usuario Administrador por defecto
        User::create([
            'username' => 'admin', // Usuario para iniciar sesión
            'password' => Hash::make('123456'), // Contraseña
            'rol_id' => 1, // 1 = Admin
        ]);
    }
}