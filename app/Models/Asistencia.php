<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asistencia extends Model
{
    use HasFactory;

    protected $fillable = [
        'docente_id',
        'fecha',
        'hora_marca',
        'estado',
    ];

    public function docente()
    {
        return $this->belongsTo(Docente::class);
    }

    public function horarios()
    {
        return $this->belongsToMany(Horario::class, 'asistencia_horario');
    }
}