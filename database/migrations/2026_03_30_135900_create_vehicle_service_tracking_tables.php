<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Configurable service rules (e.g., every 5,000km, every 3 months)
        Schema::create('vehicle_service_schedules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');                          // "5,000 km Routine Service"
            $table->string('schedule_type');                  // mileage | time_based
            $table->unsignedInteger('mileage_interval')->nullable();  // e.g., 5000
            $table->unsignedInteger('month_interval')->nullable();    // e.g., 3, 6
            $table->string('service_category');              // routine | inspection | major
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Complete service history log per vehicle
        Schema::create('vehicle_service_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('car_id')->constrained('cars')->cascadeOnDelete();
            $table->foreignUuid('schedule_id')->nullable()->constrained('vehicle_service_schedules')->nullOnDelete();
            $table->foreignUuid('performed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('service_type');                  // routine | inspection | major | repair
            $table->text('description')->nullable();
            $table->string('service_provider')->nullable();
            $table->decimal('cost', 10, 2)->nullable();
            $table->unsignedInteger('mileage_at_service');
            $table->date('service_date');
            $table->date('next_service_date')->nullable();
            $table->unsignedInteger('next_service_mileage')->nullable();
            $table->string('status')->default('completed');  // completed | scheduled | overdue
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vehicle_service_logs');
        Schema::dropIfExists('vehicle_service_schedules');
    }
};
