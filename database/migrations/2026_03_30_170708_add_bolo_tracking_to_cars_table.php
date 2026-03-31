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
            $table->string('registration_number')->nullable()->after('plate_number');
            $table->date('bolo_expiry_date')->nullable();
            $table->date('inspection_expiry_date')->nullable();
            $table->string('bolo_certificate_path')->nullable();
            $table->string('inspection_certificate_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn([
                'registration_number',
                'bolo_expiry_date',
                'inspection_expiry_date',
                'bolo_certificate_path',
                'inspection_certificate_path'
            ]);
        });
    }
};
