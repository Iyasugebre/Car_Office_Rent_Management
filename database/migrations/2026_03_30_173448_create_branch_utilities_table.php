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
        Schema::create('branch_utilities', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('office_id')->constrained('offices')->cascadeOnDelete();
            $table->string('utility_type'); // electricity, water, telephone, internet
            $table->string('provider');
            $table->string('account_number');
            $table->string('payment_cycle')->default('monthly'); // monthly, quarterly, annual
            $table->date('last_paid_at')->nullable();
            $table->date('next_due_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_utilities');
    }
};
