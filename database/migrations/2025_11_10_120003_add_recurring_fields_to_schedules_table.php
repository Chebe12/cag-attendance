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
        Schema::table('schedules', function (Blueprint $table) {

            // Add session time field
            $table->enum('session_time', [
                'morning',
                'mid-morning',
                'afternoon'
            ])->nullable()->after('day_of_week');

            // Make scheduled_date nullable for recurring schedules
            $table->date('scheduled_date')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn(['day_of_week', 'session_time', 'is_recurring']);
            $table->date('scheduled_date')->nullable(false)->change();
        });
    }
};
