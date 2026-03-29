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
        Schema::create('branch_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('reference_number')->unique();
            $table->string('branch_name');
            $table->string('location');
            $table->string('proposed_office');
            $table->text('landlord_details');
            $table->decimal('estimated_rent', 12, 2);
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->enum('status', ['draft', 'submitted', 'review', 'approved', 'rejected', 'active'])->default('submitted');
            $table->text('remarks')->nullable();
            $table->foreignUuid('requester_id')->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('branch_requests');
    }
};
