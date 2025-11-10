<?php

namespace App\Models;

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

        $today = now()->toDateString();

        if ($this->valid_from && $this->valid_from > $today) {
            return false;
        }

        if ($this->valid_until && $this->valid_until < $today) {
            return false;
        }

        return true;
    }

    public function incrementScanCount()
    {
        $this->increment('scan_count');
    }
}
