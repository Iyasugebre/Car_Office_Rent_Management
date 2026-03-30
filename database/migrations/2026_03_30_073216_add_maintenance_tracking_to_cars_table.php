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
        Schema::table('cars', function (Blueprint $table) {
            $table->unsignedInteger('mileage')->default(0)->after('make');
            $table->date('last_service_date')->nullable()->after('mileage');
            $table->unsignedInteger('last_service_mileage')->default(0)->after('last_service_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn(['mileage', 'last_service_date', 'last_service_mileage']);
        });
    }
};
