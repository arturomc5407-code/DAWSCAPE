<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partidas', function (Blueprint $table) {
            // Posición bloqueada en el ranking Top10 cuando el equipo finaliza o entra en timeout.
            // Null => el equipo sigue "vivo" (se reordena con polling).
            $table->unsignedSmallInteger('ranking_posicion_bloqueada')->nullable()->after('estado');
        });
    }

    public function down(): void
    {
        Schema::table('partidas', function (Blueprint $table) {
            $table->dropColumn('ranking_posicion_bloqueada');
        });
    }
};

