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
        Schema::table('leave_requests', function (Blueprint $table) {
            // Add total_days field (calculated, excluding holidays)
            $table->integer('total_days')->default(0)->after('end_date');

            // Add attachment_path for supporting documents
            $table->string('attachment_path')->nullable()->after('manager_notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn(['total_days', 'attachment_path']);
        });
    }
};
