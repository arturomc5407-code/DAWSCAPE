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
            $table->uuid('uuid')->unique()->after('id')->nullable();
        });

        // Generar UUIDs para registros existentes si los hubiera
        \App\Models\Partida::all()->each(function ($partida) {
            $partida->uuid = (string) \Illuminate\Support\Str::uuid();
            $partida->save();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partidas', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
};
