<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\ModelResource;

class CarResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if (Auth::user()->role == 'customer')
        {
            return [
                'id' => $this->id,
                'agency_id' => $this->agency_id,
                'model' => new ModelResource($this->model),
                'price_per_hour' => $this->price_per_hour,
                'status' => $this->status,
                'description' => $this->description,
                'image' => $this->images_paths ? asset('storage/' . $this->images_paths) : null,
                'is_featured' => $this->is_featured,
                'rating' => round($this->reviews_avg_rating, 2),
                'reviews_count' => $this->reviews_count,
            ];
        }
        return [];
    }
}
