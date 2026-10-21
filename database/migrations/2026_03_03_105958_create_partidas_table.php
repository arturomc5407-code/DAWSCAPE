<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('partidas', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('nombre_equipo');
            $table->string('clase');
            $table->integer('tiempo_segundos')->nullable(); // Tiempo total final
            $table->timestamp('inicio_juego')->nullable();  // <--- ¡ESTA ES LA CLAVE!
            $table->string('fase_actual')->default('Inicio');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partidas');
    }
};
