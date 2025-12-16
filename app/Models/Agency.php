<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Agency extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'commercial_register',
        'commercial_register_number',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cars(): HasMany
    {
        return $this->hasMany(Car::class);
    }

    public function bookings()
    {
        return $this->hasManyThrough(
            Booking::class,
            Car::class
        );
    }

    public function isActive(): bool
    {
        return $this->user->is_active;
    }

    public function isApproved(): bool
    {
        return $this->user->is_approved;
    }

    public function scopeActiveAgencies($query)
    {
        return $query->whereHas('user', function ($q) {
            $q->where('is_active', true);
        });
    }

    public function scopeApprovedAgencies($query)
    {
        return $query->whereHas('user', function ($q) {
            $q->where('is_approved', true);
        });
    }
}
