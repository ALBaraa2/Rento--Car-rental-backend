<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $agency = Agency::with('user')->where("user_id", $user->id)->first();

        if (!$agency || $user->role != 'agency') {
            return response()->json(['mesasge' => 'unauthorized']);
        }

        $bestRatingCar = Car::with(['agency.user', 'model.brand'])->byAgencyBestRated($agency->id)->limit(5)->get();
        $recentAddedCars = Car::with(['agency.user', 'model.brand'])->where('agency_id', $agency->id)->latest()->limit(5)->get();
        $featuredCars = Car::with(['agency.user', 'model.brand'])->where('agency_id', $agency->id)->featured()->limit(5)->get();

        return response()->json([
            'success' => true,
            'best_rating_cars' => $bestRatingCar,
            'recent_added_cars' => $recentAddedCars,
            'featured_cars' => $featuredCars,
        ]);
    }
}
