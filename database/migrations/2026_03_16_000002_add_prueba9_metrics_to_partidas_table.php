<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partidas', function (Blueprint $table) {
            $table->unsignedInteger('intentos_prueba9')->default(0)->after('intentos_codice');
            $table->boolean('hints_used')->default(false)->after('intentos_prueba9');
        });
    }

    public function down(): void
    {
        Schema::table('partidas', function (Blueprint $table) {
            $table->dropColumn(['intentos_prueba9', 'hints_used']);
        });
    }
};

