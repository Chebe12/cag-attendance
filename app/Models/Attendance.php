<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'qr_code_id',
        'schedule_id',
        'shift_id',
        'client_id',
        'attendance_date',
        'attendance_type',
        'check_in',
        'check_out',
        'check_in_location',
        'check_out_location',
        'check_in_ip',
        'check_out_ip',
        'check_in_latitude',
        'check_in_longitude',
        'check_out_latitude',
        'check_out_longitude',
        'status',
        'work_duration',
        'notes',
        'check_in_photo',
        'check_out_photo',
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function qrCode()
    {
        return $this->belongsTo(QrCode::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function logs()
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('attendance_date', now()->toDateString());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('attendance_date', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereYear('attendance_date', now()->year)
                     ->whereMonth('attendance_date', now()->month);
    }

    /**
     * Get the work duration in hours
     */
    public function getHoursAttribute()
    {
        if ($this->work_duration) {
            return round($this->work_duration / 60, 2);
        }
        return 0.0;
    }

    public function calculateDuration()
    {
        if ($this->check_in && $this->check_out) {
            $duration = $this->check_in->diffInMinutes($this->check_out);
            $this->work_duration = $duration;
            $this->save();
            return $duration;
        }
        return null;
    }
}
