<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'client_id',
        'shift_id',
        'scheduled_date',
        'start_time',
        'end_time',
        'day_of_week',
        'session_time',
        'is_recurring',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'is_recurring' => 'boolean',
    ];

    /**
     * Get the start and end time for a session
     */
    public static function getSessionTimes($session)
    {
        $times = [
            'morning' => ['start' => '08:30:00', 'end' => '10:00:00'],
            'mid-morning' => ['start' => '10:30:00', 'end' => '12:00:00'],
            'afternoon' => ['start' => '12:30:00', 'end' => '14:00:00'],
        ];

        return $times[$session] ?? null;
    }

    /**
     * Get formatted session time range
     */
    public function getSessionTimeRangeAttribute()
    {
        if (!$this->session_time) {
            return 'N/A';
        }

        $times = self::getSessionTimes($this->session_time);
        if (!$times) {
            return 'N/A';
        }

        return date('h:i A', strtotime($times['start'])) . ' - ' . date('h:i A', strtotime($times['end']));
    }

    /**
     * Get the actual start time for this session
     */
    public function getActualStartTimeAttribute()
    {
        if ($this->start_time) {
            return $this->start_time;
        }

        $times = self::getSessionTimes($this->session_time);
        return $times ? $times['start'] : null;
    }

    /**
     * Get the actual end time for this session
     */
    public function getActualEndTimeAttribute()
    {
        if ($this->end_time) {
            return $this->end_time;
        }

        $times = self::getSessionTimes($this->session_time);
        return $times ? $times['end'] : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attendance()
    {
        return $this->hasOne(Attendance::class);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_date', '>=', now()->toDateString())
                     ->where('status', 'scheduled');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_date', now()->toDateString());
    }
}
