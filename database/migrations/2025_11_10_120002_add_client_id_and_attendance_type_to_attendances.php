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
        Schema::table('attendances', function (Blueprint $table) {
            // Add client_id to track which client this attendance is for
            $table->foreignId('client_id')->nullable()->after('schedule_id')->constrained()->onDelete('set null');
            
            // Add attendance_type to differentiate between office work and client visits
            $table->enum('attendance_type', ['office', 'client_visit'])->default('office')->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropColumn(['client_id', 'attendance_type']);
        });
    }
};
