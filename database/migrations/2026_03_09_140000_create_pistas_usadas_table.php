<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pistas_usadas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partida_id')->constrained('partidas')->onDelete('cascade');
            $table->unsignedTinyInteger('prueba_numero');
            $table->timestamps();

            $table->unique(['partida_id', 'prueba_numero']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pistas_usadas');
    }
};
