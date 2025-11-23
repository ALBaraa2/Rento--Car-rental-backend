<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Models extends Model
{
    use HasFactory;
    protected $fillable = [
        'brand_id',
        'name',
        'year',
        'type',
        'color',
        'fuel_type',
        'seats',
        'doors',
        'transmission',
    ];

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brands::class, 'brand_id');
    }

    public function cars(): HasMany
    {
        return $this->hasMany(Cars::class, 'model_id');
    }
}
