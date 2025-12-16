<?php

namespace App\Http\Controllers\agency;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    public function allBookings()
    {
        $agency = Auth::user()->agency;

        $dates = $agency->bookings()
            ->select('start_date', 'end_date')
            ->get()
            ->flatMap(function ($booking) {
                return [$booking->start_date, $booking->end_date];
            })
            ->unique()
            ->sort()
            ->values();

        return response()->json($dates);
    }

    public function bookingsByDate(Request $request, $date)
    {
        $agency = Auth::user()->agency;

        // $bookings = $agency->bookings()
        //     ->whereDate('start_date', $date)
        //     ->with('car.model.brand')
        //     ->get();

        $bookings = Booking::with(['car.agency', 'car.model.brand'])
            ->whereHas('car.agency', function ($q) use ($agency) {
                $q->where('id', $agency->id);
            })
            ->whereDate('start_date', $date)
            ->get();

        $data = $bookings->map(function ($booking) {
            return [
                'car_model' => $booking->car->model->name ?? null,
                'brand_name' => $booking->car->model->brand->name ?? null,
                'customer_name' => $booking->customer->user->name ?? null,
                'start_time' => $booking->start_time,
                'end_time' => $booking->end_time,
                'car_image' => $booking->car->images_paths? asset('storage/' . (is_array($booking->car->images_paths) ? $booking->car->images_paths[0] : json_decode($booking->car->images_paths)[0])): null,
            ];
        });

        return response()->json($data);
    }
}
