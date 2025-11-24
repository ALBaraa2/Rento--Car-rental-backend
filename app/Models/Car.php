<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Models;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

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

    public function scopeMostBooked($query): Builder
    {
        return $query->confirmedBooking()->withCount('bookings')
                    ->orderByDesc('bookings_count');
    }

    public function scopeMostBookedBrandName(Builder $query)
    {
        return $query->join('models', 'cars.model_id', '=', 'models.id')
                    ->join('brands', 'models.brand_id', '=', 'brands.id')
                    ->join('bookings', 'bookings.car_id', '=', 'cars.id')
                    ->where('bookings.status', 'confirmed')
                    ->select('brands.id', 'brands.name', DB::raw('COUNT(bookings.id) as total_bookings'))
                    ->groupBy('brands.id', 'brands.name')
                    ->orderByDesc('total_bookings')
                    ->pluck('name');
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
