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
            $table->enum('estado', ['jugando', 'pausado', 'finalizado'])->default('jugando')->after('fase_actual');
            $table->integer('tiempo_pausado_segundos')->default(0)->after('penalizacion_segundos');
            $table->timestamp('ultima_pausa_at')->nullable()->after('tiempo_pausado_segundos');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partidas', function (Blueprint $table) {
            $table->dropColumn(['estado', 'tiempo_pausado_segundos', 'ultima_pausa_at']);
        });
    }
};
