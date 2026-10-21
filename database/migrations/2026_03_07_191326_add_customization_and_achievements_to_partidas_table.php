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
            $table->string('color_hex')->nullable()->after('clase');
            $table->string('icono')->nullable()->after('color_hex');
            $table->json('logros')->nullable()->after('fase_actual');
            // penalizacion_segundos ya existe en la migración original
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partidas', function (Blueprint $table) {
            $table->dropColumn(['color_hex', 'icono', 'logros']);
        });
    }
};
