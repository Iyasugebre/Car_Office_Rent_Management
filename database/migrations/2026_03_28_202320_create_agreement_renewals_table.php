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
        Schema::create('agreement_renewals', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('agreement_id')->constrained('agreements')->cascadeOnDelete();
            $table->enum('action', ['renew', 'amend', 'terminate'])->default('renew');
            $table->enum('status', ['pending_approval', 'approved', 'rejected'])->default('pending_approval');

            // New terms (for renew/amend)
            $table->decimal('new_monthly_rent', 12, 2)->nullable();
            $table->date('new_start_date')->nullable();
            $table->date('new_end_date')->nullable();
            $table->string('new_payment_schedule')->nullable();

            // Amendment notes
            $table->text('reason')->nullable();
            $table->text('amendment_notes')->nullable();

            // Approval
            $table->foreignUuid('approved_by')->nullable()->constrained('users');
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_remarks')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agreement_renewals');
    }
};
