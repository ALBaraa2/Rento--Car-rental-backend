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
            $images = $this->images_paths;

            if (is_string($images)) {
                $images = json_decode($images, true);
            }
            return [
                'id' => $this->id,
                'agency_id' => $this->agency_id,
                'model_name' => $this->model->name,
                'model_year' => $this->model->year,
                'type' => $this->model->type,
                'color' => $this->model->color,
                'fuel_type' => $this->model->fuel_type,
                'seats' => $this->model->seats,
                'doors' => $this->model->doors,
                'transmission' => $this->model->transmission,
                'brand' => $this->model->brand->name,
                'price_per_hour' => $this->price_per_hour,
                'status' => $this->status,
                'description' => $this->description,
                'is_featured' => $this->is_featured,
                'rating' => round($this->reviews_avg_rating, 2),
                'reviews_count' => $this->reviews_count,
                'images' => $this->images_paths
                ? array_map(
                    fn ($image) => asset('storage/' . $image),
                    $images
                )
                : [],
            ];
        }
        return [];
    }
}
