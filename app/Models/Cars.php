<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cars extends Model
{
    use HasFactory;
    protected $fillable = [
        'model_id',
        'registration_number',
        'price_per_day',
        'status',
        'description',
        'images_paths',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function agency(): BelongsTo
    {
        return $this->belongsTo(Agency::class);
    }

    public function model(): BelongsTo
    {
        return $this->belongsTo(Models::class);
    }
}
