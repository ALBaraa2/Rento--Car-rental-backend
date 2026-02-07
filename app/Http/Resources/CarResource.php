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

            $isDetails = $request->routeIs('customer.car.details');

            return [
                'id' => $this->id,
                'agency_id' => $this->agency_id,
                'agency_address' => $this->agency->user->address,
                'agency_name' => $this->agency->user->name,
                'model_name' => $this->model->name,
                'brand' => $this->model->brand->name,
                'model_year' => $this->model->year,
                'type' => $this->model->brand->type,
                'color' => $this->color,
                'fuel_type' => $this->fuel_type,
                'seats' => $this->seats,
                'doors' => $this->doors,
                'transmission' => $this->transmission,
                'price_per_hour' => $this->price_per_hour,
                'status' => $this->status,
                'description' => $this->description,
                'is_featured' => $this->is_featured,
                'rating' => $this->reviews_avg_rating ? round($this->reviews_avg_rating, 2) : null,
                'reviews_count' => $this->reviews_count,
                'images' => $isDetails
                    ? array_map(fn ($img) => asset('storage/' . $img), $images ?? [])
                    : (isset($images[0]) ? asset('storage/' . $images[0]) : null),
            ];
        }
        return [];
    }
}
