<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'head_of_department',
        'status',
    ];

    /**
     * Get the head of department (user)
     */
    public function head()
    {
        return $this->belongsTo(User::class, 'head_of_department');
    }

    /**
     * Get all users in this department
     */
    public function users()
    {
        return $this->hasMany(User::class, 'department_id');
    }

    /**
     * Scope for active departments
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
