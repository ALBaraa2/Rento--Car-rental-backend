<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use HasFactory;
    protected $fillable = [
        'booking_id',
        'customer_id',
        'amount',
        'currency',
        'provider',
        'payment_method',
        'status',
        'transaction_id',
        'order_id',
        'payment_intent_id',
        'receipt_url',
        'refunded_amount',
        'error_code',
        'error_message',
        'metadata',
        'initiated_at',
        'authorized_at',
        'captured_at',
        'cancelled_at',
        'refunded_at',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}
