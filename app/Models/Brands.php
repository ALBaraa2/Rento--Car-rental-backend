<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brands extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'country',
        'type',
    ];

    public function models(): HasMany
    {
        return $this->hasMany(Models::class);
    }
}
