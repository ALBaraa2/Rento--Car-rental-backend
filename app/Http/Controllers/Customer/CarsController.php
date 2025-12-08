<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Car;
use App\Http\Resources\CarResource;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

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

        $cars = Car::with(['agency.user', 'model.brand', 'reviews'])->where('agency_id', $id)->Available()->withAvg('reviews as avarage_rating', 'rating')
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

    public function book($id, Request $request)
    {
        $car = Car::Available()->find($id);

        if (!$car) {
            return response()->json(['message' => 'Car not available'], 404);
        }

        $validated = $request->validate([
            'start_date' =>'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'start_time'=> 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $start = Carbon::parse($validated['start_date'] . ' ' . $validated['start_time']);
        $end = Carbon::parse($validated['end_date'] . ' ' . $validated['end_time']);

        if ($start->lt(Carbon::now()) || $end->lt(Carbon::now())) {
            return response()->json([
                'message' => 'You cannot make a booking for a date or time that has already passed. Please choose a future time.'
            ], 422);
        }

        if ($end->lessThanOrEqualTo($start)) {
            return response()->json(['message' => 'End datetime must be after start datetime'], 422);
        }

        $conflict = $car->bookings()
        ->where(function($q) use ($start, $end) {
            $q->where(function($query) use ($start, $end) {
                $query->where('start_date', '<=', $end->toDateString())
                    ->where('end_date', '>=', $start->toDateString());
            })
            ->where(function($query) use ($start, $end) {
                $query->where(function($t) use ($start, $end) {
                    if ($start->toDateString() === $end->toDateString()) {
                        $t->where('start_time', '<', $end->format('H:i'))
                        ->where('end_time', '>', $start->format('H:i'));
                    }
                });
            });
        })
        ->exists();

        if ($conflict) {
            return response()->json(['message' => 'Car is already booked for the selected period'], 409);
        }

        $hours = $start->diffInHours($end);
        $total_price = $car->price_per_hour * $hours;

        $booking = $car->bookings()->create([
            'customer_id' => $request->user()->customer->id,
            'car_id' => $car->id,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'status' => 'pending',
            'total_price' => $total_price,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Car booked successfully',
            'booking_id' => $booking->id,
            'total_price' => $total_price
        ]);
    }
}
