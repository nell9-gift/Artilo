<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artisans', function (Blueprint $table) {
            $table->string('mobile_money_number')->nullable()->after('longitude');
            $table->enum('mobile_money_operator', ['moov', 'yas'])->nullable()->after('mobile_money_number');
            $table->boolean('is_available')->default(false)->after('mobile_money_operator');
            $table->integer('max_distance_km')->nullable()->after('is_available');
        });
    }

    public function down(): void
    {
        Schema::table('artisans', function (Blueprint $table) {
            $table->dropColumn([
                'mobile_money_number',
                'mobile_money_operator',
                'is_available',
                'max_distance_km',
            ]);
        });
    }
};