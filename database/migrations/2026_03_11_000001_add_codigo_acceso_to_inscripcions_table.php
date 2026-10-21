<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega la columna codigo_acceso a la tabla inscripcions.
     * Reemplaza la lógica de QR por un código de 6 caracteres alfanuméricos.
     */
    public function up(): void
    {
        Schema::table('inscripcions', function (Blueprint $table) {
            // Agregar columna codigo_acceso (ej: X7B9A2)
            $table->string('codigo_acceso', 6)->unique()->after('qr_token')->nullable();
        });
    }

    /**
     * Revierte la migración.
     */
    public function down(): void
    {
        Schema::table('inscripcions', function (Blueprint $table) {
            $table->dropColumn('codigo_acceso');
        });
    }
};
