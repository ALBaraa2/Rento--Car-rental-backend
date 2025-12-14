<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'customer_id',
        'car_id',
        'user_id',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'total_price',
        'status',
    ];

    public function car(): BelongsTo
    {
        return $this->belongsTo(Car::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
}
