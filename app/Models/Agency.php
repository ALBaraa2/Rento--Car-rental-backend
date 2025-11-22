<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Agency extends Model
{
    protected $fillable = [
        'user_id',
        'commercial_register',
        'contact_email',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
