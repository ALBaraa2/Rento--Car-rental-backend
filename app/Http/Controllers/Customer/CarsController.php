<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Car;
use App\Http\Resources\CarResource;
use Illuminate\Http\Request;

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

    public function search(Request $request, string $id)
    {
        $model = $request->input('model');
        $brand = $request->input('brand');
        $type = $request->input('type');
        $min_price = $request->input('min_price');
        $max_price = $request->input('max_price');
        $rating = $request->input('rating');

        $agency = Agency::with('user')->findOrFail($id);

        if (!$agency->isActive() || !$agency->isApproved()) {
            return response()->json(['message' => 'Agency not Found'], 404);
        }

        $cars = Car::with(['agency.user', 'model.brand', 'reviews'])->where('agency_id', $id)->Available()
            ->when($model, function ($query) use ($model) {
                $query->whereHas('model', function ($q) use ($model) {
                    $q->where('name', 'ilike', '%' . $model . '%');
                });
            })
            ->when($brand, function ($query) use ($brand) {
                $query->whereHas('model.brand', function ($q) use ($brand) {
                    $q->where('name', 'ilike', '%' . $brand . '%');
                });
            })
            ->when($type, function ($query) use ($type) {
                $query->whereHas('model', function ($q) use ($type) {
                    $q->where('type', 'ilike', '%' . $type . '%');
                });
            })
            ->when($rating, function ($query) use ($rating) {
                $query->whereRaw('(select avg(rating) from reviews where reviews.car_id = cars.id) >= ?', [$rating]);
            })
            ->when($min_price, function ($query) use ($min_price) {
                $query->where('price_per_hour', '>=', $min_price);
            })
            ->when($max_price, function ($query) use ($max_price) {
                $query->where('price_per_hour', '<=', $max_price);
            })
            ->get();

        return response()->json([
            'success' => true,
            'count' => $cars->count(),
            'cars' => $cars
        ]);
    }
}
