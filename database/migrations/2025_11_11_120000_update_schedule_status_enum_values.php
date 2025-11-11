<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update status enum values for schedules table
        DB::statement("ALTER TABLE schedules MODIFY COLUMN status ENUM('draft', 'scheduled', 'pending', 'canceled') DEFAULT 'draft'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to old status values
        DB::statement("ALTER TABLE schedules MODIFY COLUMN status ENUM('scheduled', 'completed', 'cancelled', 'rescheduled') DEFAULT 'scheduled'");
    }
};
