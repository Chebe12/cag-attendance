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
        // Clients Table
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('contact_person')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('postal_code')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Shifts Table
        Schema::create('shifts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // e.g., "Morning Shift", "Mid-Morning Shift", "Afternoon Shift"
            $table->time('start_time');
            $table->time('end_time');
            $table->string('color')->default('#3B82F6'); // For UI display
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Schedules Table - Instructor assignments to clients
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Instructor
            $table->foreignId('client_id')->constrained()->onDelete('cascade');
            $table->foreignId('shift_id')->nullable()->constrained()->onDelete('set null');
            $table->date('scheduled_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->enum('day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])->nullable();
            $table->boolean('is_recurring')->default(false); // For recurring schedules
            $table->enum('status', ['scheduled', 'completed', 'cancelled', 'missed'])->default('scheduled');
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // Admin who created
            $table->timestamps();
            $table->softDeletes();
        });

        // QR Codes Table
        Schema::create('qr_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // Unique QR code identifier
            $table->string('qr_image_path')->nullable(); // Path to stored QR image
            $table->enum('type', ['daily', 'weekly', 'permanent'])->default('daily');
            $table->date('valid_from')->nullable();
            $table->date('valid_until')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('scan_count')->default(0); // Track usage
            $table->text('metadata')->nullable(); // JSON for additional data
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // Attendance Table
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('qr_code_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('schedule_id')->nullable()->constrained()->onDelete('set null'); // Link to schedule if applicable
            $table->foreignId('shift_id')->nullable()->constrained()->onDelete('set null');
            $table->date('attendance_date');
            $table->timestamp('check_in')->nullable();
            $table->timestamp('check_out')->nullable();
            $table->string('check_in_location')->nullable(); // GPS or location name
            $table->string('check_out_location')->nullable();
            $table->string('check_in_ip')->nullable();
            $table->string('check_out_ip')->nullable();
            $table->enum('status', ['present', 'late', 'absent', 'half_day', 'on_leave'])->default('present');
            $table->integer('work_duration')->nullable(); // In minutes
            $table->text('notes')->nullable();
            $table->text('check_in_photo')->nullable(); // Optional selfie
            $table->text('check_out_photo')->nullable();
            $table->timestamps();
        });

        // Leave Requests Table
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('leave_type', ['sick', 'vacation', 'personal', 'emergency', 'other'])->default('vacation');
            $table->text('reason');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });

        // Notifications Table
        Schema::create('notifications_custom', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('message');
            $table->enum('type', ['schedule', 'reminder', 'announcement', 'alert'])->default('announcement');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->text('metadata')->nullable(); // JSON for additional data
            $table->timestamps();
        });

        // Announcements Table
        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->enum('priority', ['low', 'medium', 'high', 'urgent'])->default('medium');
            $table->date('publish_date')->nullable();
            $table->date('expiry_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        // Attendance Logs (for audit trail)
        Schema::create('attendance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('attendance_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('action'); // e.g., "check_in", "check_out", "modified", "deleted"
            $table->text('old_data')->nullable();
            $table->text('new_data')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendance_logs');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('notifications_custom');
        Schema::dropIfExists('leave_requests');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('qr_codes');
        Schema::dropIfExists('schedules');
        Schema::dropIfExists('shifts');
        Schema::dropIfExists('clients');
    }
};
