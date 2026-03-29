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
            $table->enum('legal_status', ['pending', 'approved', 'rejected'])->default('pending')->after('status');
            $table->foreignUuid('legal_reviewer_id')->nullable()->after('legal_status')->constrained('users');
            $table->timestamp('legal_reviewed_at')->nullable()->after('legal_reviewer_id');
            $table->text('legal_remarks')->nullable()->after('legal_reviewed_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('agreements', function (Blueprint $table) {
            //
        });
    }
};
