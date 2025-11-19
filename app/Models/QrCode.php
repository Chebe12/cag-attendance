<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'qr_image_path',
        'type',
        'valid_from',
        'valid_until',
        'is_active',
        'scan_count',
        'metadata',
        'created_by',
    ];

    protected $casts = [
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean',
        'metadata' => 'array',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                     ->where(function($q) {
                         $q->whereNull('valid_until')
                           ->orWhere('valid_until', '>=', now()->toDateString());
                     });
    }

    public function isValid()
    {
        if (!$this->is_active) {
            return false;
        }

        $today = Carbon::today();

        // Check if valid_from is in the future
        if ($this->valid_from) {
            $validFrom = Carbon::parse($this->valid_from)->startOfDay();
            if ($validFrom->greaterThan($today)) {
                return false;
            }
        }

        // Check if valid_until is in the past
        if ($this->valid_until) {
            $validUntil = Carbon::parse($this->valid_until)->endOfDay();
            if ($validUntil->lessThan($today)) {
                return false;
            }
        }

        return true;
    }

    public function incrementScanCount()
    {
        $this->increment('scan_count');
    }
}
