<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('artisans', function (Blueprint $table) {
            $table->dropColumn('is_available');
            $table->time('availability_start_time')->nullable()->after('mobile_money_operator');
            $table->time('availability_end_time')->nullable()->after('availability_start_time');
        });
    }

    public function down(): void
    {
        Schema::table('artisans', function (Blueprint $table) {
            $table->dropColumn(['availability_start_time', 'availability_end_time']);
            $table->boolean('is_available')->default(false);
        });
    }
};