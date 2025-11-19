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
            // Add category reference
            $table->unsignedBigInteger('category_id')->nullable()->after('id');
            $table->foreign('category_id')->references('id')->on('schedule_categories')->onDelete('cascade');

            // Add draft status - keeps schedules as draft until published
            $table->enum('draft_status', ['draft', 'published'])->default('published')->after('status');

            // Make day_of_week required for recurring schedules
            $table->enum('day_of_week', [
                'monday',
                'tuesday',
                'wednesday',
                'thursday',
                'friday',
                'saturday',
                'sunday'
            ])->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn(['category_id', 'draft_status']);
        });
    }
};
