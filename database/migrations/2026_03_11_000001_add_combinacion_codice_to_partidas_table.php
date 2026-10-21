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
        Schema::table('partidas', function (Blueprint $table) {
            // Combinación de 4 dígitos únicos para la prueba 7 Códice Oculto
            // Se guarda como JSON: [3, 7, 1, 9] (orden de los 4 slots del candado)
            $table->json('combinacion_codice')->nullable()->after('logros');

            // Contador de intentos fallidos en el candado (para mostrar pista extra)
            $table->integer('intentos_codice')->default(0)->after('combinacion_codice');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partidas', function (Blueprint $table) {
            $table->dropColumn(['combinacion_codice', 'intentos_codice']);
        });
    }
};
