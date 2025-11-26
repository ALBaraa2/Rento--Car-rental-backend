<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class ModelResource extends JsonResource
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
                'name' => $this->name,
                'year' => $this->year,
                'type' => $this->type,
                'color' => $this->color,
                'fuel_type' => $this->fuel_type,
                'seats' => $this->seats,
                'doors' => $this->doors,
                'transmission' => $this->transmission,
                'brand' => $this->brand->name,
            ];
        }
        return [];
    }
}
