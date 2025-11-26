<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\AgencyResource;
use App\Models\Agency;
use App\Models\Car;
use App\Http\Resources\CarResource;
use App\Models\Booking;

class CarsController extends Controller
{
    public function agenciesCars(string $id)
    {
        $agency = Agency::find($id);

        if (!$agency) {
            return response()->json(['message' => 'Agency not found'], 404);
        }

        $agenciesCars = Car::with(['reviews', 'model.brand'])->byAgencyBestRated($id)->get();

        return response()->json([
            'success' => true,
            'agency_name' => $agency->user->name,
            'agency_id' => $agency->id,
            'agency_adderss' => $agency->user->address,
            'agenciesCars' => $agenciesCars->map(function ($car) {
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
