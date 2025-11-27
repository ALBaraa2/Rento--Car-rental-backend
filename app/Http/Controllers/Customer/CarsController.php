<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Car;
use App\Http\Resources\CarResource;

class CarsController extends Controller
{
    public function agencyCars(string $id)
    {
        $agency = Agency::with('user')->findOrFail($id);

        if (!$agency->isActive() || !$agency->isApproved()) {
            return response()->json(['message' => 'Agency not Found'], 404);
        }

        $agencyCars = Car::with(['reviews', 'model.brand', 'agency.user'])->byAgencyBestRated($id)->get();

        return response()->json([
            'success' => true,
            'agency_name' => $agency->user->name,
            'agency_id' => $agency->id,
            'agency_adderss' => $agency->user->address,
            'agencyCars' => $agencyCars->map(function ($car) {
                return [
                    'id' => $car->id,
                    'brand' => $car->model->brand->name ?? null,
                    'model' => $car->model->name ?? null,
                    'price_per_hour' => $car->price_per_hour,
                    'reviews_avg_rating' => $car->reviews_avg_rating ? round($car->reviews_avg_rating, 2) : null,
                ];
            }),
        ]);
    }

    public function show(string $id)
    {
        $car = Car::with(['bookings', 'reviews'])->where('id', $id)->withAvg('reviews', 'rating')->withCount('reviews')->get();

        if (!$car) {
            return response()->json(['message' => 'Car not found'], 404);
        }

        return response()->json([
            'success' => true,
            'car' => CarResource::collection($car),
        ]);
    }
}
