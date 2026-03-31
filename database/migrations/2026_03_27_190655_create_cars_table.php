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
        Schema::create('cars', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('office_id');
            $table->foreign('office_id')->references('id')->on('offices')->onDelete('cascade');
            $table->string('make');
            $table->string('model');
            $table->integer('year');
            $table->string('plate_number')->unique();
            $table->decimal('price_per_day', 10, 2);
            $table->string('status')->default('available');
            $table->string('image_url')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cars');
    }
};
