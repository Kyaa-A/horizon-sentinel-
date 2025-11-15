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
        Schema::create('leave_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('leave_type', [
                'paid_time_off',
                'unpaid_leave',
                'sick_leave',
                'vacation',
            ]);
            $table->decimal('total_allocated', 5, 2)->default(0); // Annual entitlement
            $table->decimal('used', 5, 2)->default(0); // Days consumed
            $table->decimal('pending', 5, 2)->default(0); // Days in pending requests
            $table->decimal('available', 5, 2)->default(0); // Calculated field
            $table->year('year'); // Fiscal year
            $table->timestamps();

            // Unique constraint: one balance record per user per leave type per year
            $table->unique(['user_id', 'leave_type', 'year']);

            // Indexes for performance
            $table->index('user_id');
            $table->index('year');
            $table->index(['user_id', 'year']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_balances');
    }
};
