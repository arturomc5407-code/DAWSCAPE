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
        Schema::table('inscripcions', function (Blueprint $table) {
            $table->boolean('aceptado_manualmente')->default(false)->after('codigo_acceso');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inscripcions', function (Blueprint $table) {
            $table->dropColumn('aceptado_manualmente');
        });
    }
};
