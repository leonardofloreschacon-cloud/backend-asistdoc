<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // 1. Agrega esta línea para forzar el uso de tu tabla en español
    protected $table = 'usuarios';

    protected $fillable = [
        'username',
        'password',
        'rol_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'rol_id');
    }
    public function docente()
{
    return $this->hasOne(Docente::class, 'usuario_id');
}
}