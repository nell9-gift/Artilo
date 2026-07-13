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
        Schema::table('missions', function (Blueprint $table) {
            // Date à laquelle le client souhaite que les travaux commencent
            $table->date('date_souhaitee')->nullable()->after('longitude');

            // Photos du chantier envoyées par le client (chemins stockés en JSON)
            $table->json('photos')->nullable()->after('date_souhaitee');

            // Budget prévisionnel indiqué par le client (optionnel)
            $table->unsignedInteger('budget_previsionnel')->nullable()->after('photos');

            // Horodatages du cycle de vie prestataire (utilisés dans le modèle Mission)
            $table->timestamp('acceptee_le')->nullable()->after('affectee_le');
            $table->timestamp('refusee_le')->nullable()->after('acceptee_le');
            $table->string('refus_motif')->nullable()->after('refusee_le');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('missions', function (Blueprint $table) {
            $table->dropColumn([
                'date_souhaitee',
                'photos',
                'budget_previsionnel',
                'acceptee_le',
                'refusee_le',
                'refus_motif',
            ]);
        });
    }
};