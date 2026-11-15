<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('docente_id');
            $table->date('fecha');
            $table->time('hora_marca');
            $table->string('estado', 20);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};