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
            // Sécurité - Pièce d'identité
            $table->string('identity_document_type')->nullable()->comment('carte_nationale, passeport, permis');
            $table->string('identity_document_number')->nullable();
            $table->string('identity_photo_recto')->nullable();
            $table->string('identity_photo_verso')->nullable();
            $table->string('identity_selfie')->nullable();
            $table->date('identity_expiration_date')->nullable();

            // Professionnel - Métier et spécialités
            $table->string('main_profession')->nullable();
            $table->json('sub_specialties')->nullable();
            $table->json('diplomas')->nullable();
            $table->json('certifications')->nullable();
            $table->string('company_name')->nullable();
            $table->string('company_registration_number')->nullable();

            // Zone d'intervention
            $table->string('intervention_region')->nullable();
            $table->string('intervention_prefecture')->nullable();
            $table->string('intervention_city')->nullable();

            // Langues parlées
            $table->json('languages')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('artisans', function (Blueprint $table) {
            $table->dropColumn([
                'identity_document_type',
                'identity_document_number',
                'identity_photo_recto',
                'identity_photo_verso',
                'identity_selfie',
                'identity_expiration_date',
                'main_profession',
                'sub_specialties',
                'diplomas',
                'certifications',
                'company_name',
                'company_registration_number',
                'intervention_region',
                'intervention_prefecture',
                'intervention_city',
                'languages',
            ]);
        });
    }
};
