<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artisans', function (Blueprint $table) {
            if (!Schema::hasColumn('artisans', 'est_disponible')) {
                $table->boolean('est_disponible')->default(true)->after('max_distance_km');
            }
            if (!Schema::hasColumn('artisans', 'score_interne')) {
                $table->integer('score_interne')->default(50)->after('est_disponible');
            }
            if (!Schema::hasColumn('artisans', 'niveau')) {
                $table->enum('niveau', ['debutant', 'confirme', 'expert'])->nullable()->after('score_interne');
            }
        });
    }

    public function down(): void
    {
        Schema::table('artisans', function (Blueprint $table) {
            $table->dropColumn(['est_disponible', 'score_interne', 'niveau']);
        });
    }
};