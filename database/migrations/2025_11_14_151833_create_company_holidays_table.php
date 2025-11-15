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
        Schema::create('company_holidays', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Holiday name (e.g., "Christmas Day", "Independence Day")
            $table->date('date'); // The date of the holiday
            $table->boolean('is_recurring')->default(false); // If it repeats annually
            $table->string('region')->nullable(); // For location-specific holidays (e.g., "US", "UK", "CA")
            $table->timestamps();

            // Index on date for quick lookups
            $table->index('date');
            $table->index(['date', 'region']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_holidays');
    }
};
