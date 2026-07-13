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
        Schema::table('artisans', function (Blueprint $table) {
            // Ajout de la colonne metier_id après main_profession
            $table->foreignId('metier_id')
                ->nullable()
                ->after('main_profession')
                ->constrained('metiers')
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('artisans', function (Blueprint $table) {
            // Suppression de la colonne et de sa contrainte
            $table->dropConstrainedForeignId('metier_id');
        });
    }
};