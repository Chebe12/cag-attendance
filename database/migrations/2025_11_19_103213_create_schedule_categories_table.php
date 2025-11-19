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
        Schema::create('schedule_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "First Term 2020/2021", "Marketing 2024", etc.
            $table->string('code')->unique(); // Short code for easy reference
            $table->text('description')->nullable();
            $table->date('start_date'); // When this schedule cycle starts
            $table->date('end_date'); // When this schedule cycle ends
            $table->enum('status', ['draft', 'active', 'completed', 'archived'])->default('draft');
            $table->unsignedBigInteger('created_by');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedule_categories');
    }
};
