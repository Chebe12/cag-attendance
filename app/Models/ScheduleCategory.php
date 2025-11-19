<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduleCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'start_date',
        'end_date',
        'status',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the creator of this category
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all schedules in this category
     */
    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'category_id');
    }

    /**
     * Get only published schedules in this category
     */
    public function publishedSchedules()
    {
        return $this->hasMany(Schedule::class, 'category_id')
                    ->where('draft_status', 'published');
    }

    /**
     * Get only draft schedules in this category
     */
    public function draftSchedules()
    {
        return $this->hasMany(Schedule::class, 'category_id')
                    ->where('draft_status', 'draft');
    }

    /**
     * Scope to get active categories
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to get draft categories
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Check if category is currently active based on dates
     */
    public function isCurrentlyActive()
    {
        $now = now()->toDateString();
        return $this->status === 'active'
            && $this->start_date <= $now
            && $this->end_date >= $now;
    }

    /**
     * Get the duration of this category in days
     */
    public function getDurationAttribute()
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }
}
