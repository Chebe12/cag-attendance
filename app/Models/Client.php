<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'contact_person',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'postal_code',
        'status',
        'notes',
    ];

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function shifts()
    {
        return $this->hasManyThrough(Shift::class, Schedule::class, 'client_id', 'id', 'id', 'shift_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Generate a unique client code from the client name
     */
    public static function generateCode($name)
    {
        // Convert name to uppercase and take first 3 letters
        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $name), 0, 3));

        // If less than 3 letters, pad with 'X'
        $prefix = str_pad($prefix, 3, 'X');

        // Find the last client with this prefix
        $lastClient = self::where('code', 'LIKE', $prefix . '%')
            ->orderBy('code', 'desc')
            ->first();

        if ($lastClient) {
            // Extract the number part and increment
            $lastNumber = (int) substr($lastClient->code, 3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        // Format: PREFIX001, PREFIX002, etc.
        return $prefix . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }
}
