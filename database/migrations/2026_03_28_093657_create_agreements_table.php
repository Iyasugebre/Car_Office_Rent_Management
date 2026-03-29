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
        Schema::create('agreements', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('branch_request_id')->constrained('branch_requests')->cascadeOnDelete();
            $table->string('agreement_id')->unique();
            $table->string('landlord_name');
            $table->text('property_address');
            $table->decimal('monthly_rent', 12, 2);
            $table->string('payment_schedule')->default('Monthly');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('contract_path')->nullable();
            $table->enum('status', ['draft', 'active', 'expired', 'terminated'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agreements');
    }
};
