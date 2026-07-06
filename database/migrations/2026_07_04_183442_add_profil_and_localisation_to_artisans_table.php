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
        $table->text('description')->nullable()->after('status');
        $table->integer('years_experience')->default(0)->after('description');  // ← renommé
        $table->json('photos')->nullable()->after('years_experience');
        $table->decimal('average_rating', 3, 2)->default(0)->after('photos');   // ← renommé
        $table->string('address')->nullable()->after('intervention_area');      // ← renommé
        $table->decimal('latitude', 10, 7)->nullable()->after('address');
        $table->decimal('longitude', 10, 7)->nullable()->after('latitude');
    });
}

public function down(): void
{
    Schema::table('artisans', function (Blueprint $table) {
        $table->dropColumn([
            'description',
            'years_experience',
            'photos',
            'average_rating',
            'address',
            'latitude',
            'longitude'
        ]);
    });
}
};
