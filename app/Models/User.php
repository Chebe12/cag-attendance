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
}
