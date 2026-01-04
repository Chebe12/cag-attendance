<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    protected $fillable = [
        'employee_no',
        'firstname',
        'middlename',
        'lastname',
        'email',
        'password',
        'phone',
        'avatar',
        'department', // Legacy field - kept for backward compatibility
        'department_id', // New foreign key to departments table
        'position',
        'user_type',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function username()
    {
        return 'employee_no';
    }

    // Accessor for full name
    public function getFullNameAttribute()
    {
        return trim("{$this->firstname} {$this->middlename} {$this->lastname}");
    }

    // Accessor for name (alias for full_name)
    public function getNameAttribute()
    {
        return $this->full_name;
    }

    // Check if user is admin
    public function isAdmin()
    {
        return $this->user_type === 'admin';
    }

    // Check if user is instructor
    public function isInstructor()
    {
        return $this->user_type === 'instructor';
    }

    // Check if user is office staff
    public function isOfficeStaff()
    {
        return $this->user_type === 'office_staff';
    }

    // Relationships
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function leaveRequests()
    {
        return $this->hasMany(LeaveRequest::class);
    }

    public function createdSchedules()
    {
        return $this->hasMany(Schedule::class, 'created_by');
    }

    public function createdQrCodes()
    {
        return $this->hasMany(QrCode::class, 'created_by');
    }

    public function headedDepartment()
    {
        return $this->hasOne(Department::class, 'head_of_department');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class)->orderBy('created_at', 'desc');
    }

    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class)->where('is_read', false)->orderBy('created_at', 'desc');
    }

    // Scope for active users
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope for instructors
    public function scopeInstructors($query)
    {
        return $query->where('user_type', 'instructor');
    }

    // Scope for office staff
    public function scopeOfficeStaff($query)
    {
        return $query->where('user_type', 'office_staff');
    }

    /**
     * Generate a unique employee number with CAG- prefix
     */
    public static function generateEmployeeNo()
    {
        // Find the last employee number with CAG- prefix
        $lastUser = self::where('employee_no', 'LIKE', 'CAG-%')
            ->orderBy('employee_no', 'desc')
            ->first();

        if ($lastUser) {
            // Extract the number part and increment
            $lastNumber = (int) substr($lastUser->employee_no, 4); // Skip "CAG-"
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        // Format: CAG-0001, CAG-0002, etc.
        return 'CAG-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
