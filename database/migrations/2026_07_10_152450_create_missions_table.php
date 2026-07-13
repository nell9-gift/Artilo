<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Modification de l'enum pour ajouter les nouveaux statuts
        DB::statement("ALTER TABLE missions MODIFY COLUMN statut ENUM(
            'en_attente',
            'affectee',
            'acceptee',
            'diagnostic_effectue',
            'devis_en_attente_validation',
            'devis_valide',
            'devis_envoye',
            'devis_refuse',
            'acompte_regle',
            'en_cours',
            'terminee_prestataire',
            'validee_client',
            'solde_regle',
            'payee',
            'annulee'
        ) DEFAULT 'en_attente'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE missions MODIFY COLUMN statut ENUM(
            'en_attente',
            'affectee',
            'acceptee',
            'devis_soumis',
            'devis_valide',
            'en_cours',
            'terminee',
            'payee',
            'annulee'
        ) DEFAULT 'en_attente'");
    }
};