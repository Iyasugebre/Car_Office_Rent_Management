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
        Schema::table('agreements', function (Blueprint $table) {
            $table->date('last_rent_paid_at')->nullable()->after('payment_schedule');
            $table->date('next_rent_due_at')->nullable()->after('last_rent_paid_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agreements', function (Blueprint $table) {
            $table->dropColumn(['last_rent_paid_at', 'next_rent_due_at']);
        });
    }
};
