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
        Schema::create('service_requests', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('car_id')->constrained('cars')->cascadeOnDelete();
            $table->foreignUuid('requester_id')->constrained('users');
            $table->foreignUuid('fleet_manager_id')->nullable()->constrained('users');
            
            $table->text('problem_description');
            $table->string('service_type');
            $table->string('urgency_level')->default('medium');
            $table->string('status')->default('pending');
            
            $table->string('service_provider')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->string('service_report_path')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};
