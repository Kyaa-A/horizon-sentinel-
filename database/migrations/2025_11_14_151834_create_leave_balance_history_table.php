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
        Schema::create('leave_balance_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('leave_balance_id')->constrained()->cascadeOnDelete();
            $table->decimal('change_amount', 5, 2); // Positive for accrual, negative for consumption
            $table->enum('change_type', [
                'accrual',      // Regular accrual (monthly/yearly)
                'consumption',  // Leave taken
                'adjustment',   // Manual HR adjustment
                'carryover',    // Year-end carryover
            ]);
            $table->foreignId('leave_request_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('performed_by_user_id')->constrained('users')->cascadeOnDelete();
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->useCurrent(); // Only created_at, no updates

            // Indexes for performance
            $table->index('leave_balance_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_balance_history');
    }
};
