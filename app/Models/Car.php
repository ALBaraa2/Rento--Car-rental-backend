<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Models;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Car extends Model
{
    use HasFactory;
    protected $fillable = [
        'model_id',
        'registration_number',
        'price_per_hour',
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

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function scopeAvailable($query)
    {
        return $query->where('status', 'available');
    }

    public function scopeFeatured($query)
    {
        return $query->available()->where('is_featured', true);
    }

    public function scopeConfirmedBooking($query): Builder
    {
        return $query->whereHas('bookings', function (Builder $q) {
                        $q->where('status', 'confirmed');
                    });
    }

    public function scopeBestRated($query): Builder
    {
        return $query->confirmedBooking()->reviewsCount()
                    ->whereHas('reviews')
                    ->withAvg('reviews as average_rating', 'rating')
                    ->orderByDesc('average_rating');
    }

    public function scopeReviewsCount($query): Builder
    {
        return $query->withCount('reviews');
    }
}
