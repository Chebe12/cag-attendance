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
        Schema::table('schedules', function (Blueprint $table) {
            // Make shift_id nullable since we're moving to session-based system
            $table->unsignedBigInteger('shift_id')->nullable()->change();

            // Update session_time to be required (no longer nullable)
            // Already exists as nullable, make it required
            DB::statement("ALTER TABLE schedules MODIFY COLUMN session_time ENUM('morning', 'mid-morning', 'afternoon') NOT NULL");

            // Remove start_time and end_time as they'll be determined by session
            // Keep them for backward compatibility but make them nullable
            $table->time('start_time')->nullable()->change();
            $table->time('end_time')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            // Revert changes
            $table->unsignedBigInteger('shift_id')->nullable(false)->change();
            DB::statement("ALTER TABLE schedules MODIFY COLUMN session_time ENUM('morning', 'mid-morning', 'afternoon') NULL");
            $table->time('start_time')->nullable(false)->change();
            $table->time('end_time')->nullable(false)->change();
        });
    }
};
