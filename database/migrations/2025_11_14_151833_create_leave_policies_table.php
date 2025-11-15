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
        Schema::create('leave_policies', function (Blueprint $table) {
            $table->id();
            $table->enum('policy_type', [
                'blackout_period',      // Dates when leave is restricted
                'minimum_notice',       // Minimum days notice required
                'max_consecutive_days', // Maximum consecutive days allowed
            ]);
            $table->enum('leave_type', [
                'paid_time_off',
                'unpaid_leave',
                'sick_leave',
                'vacation',
            ])->nullable(); // Null means applies to all leave types
            $table->json('config_json'); // Flexible configuration storage
            // Example configs:
            // blackout_period: {"start_date": "2024-12-24", "end_date": "2024-12-31", "reason": "Year-end closing"}
            // minimum_notice: {"days": 14}
            // max_consecutive_days: {"days": 21, "allow_override": true}
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes
            $table->index('policy_type');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leave_policies');
    }
};
