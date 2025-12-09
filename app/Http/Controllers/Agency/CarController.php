<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $agency = Agency::with('user')->where("user_id", $user->id)->first();

        if (!$agency || $user->role != 'agency') {
            return response()->json(['mesasge' => 'unauthorized']);
        }

        $cars = $agency->cars()->with(['model.brand'])->get();

        if ($cars->count() == 0) {
            return response()->json(['message'=> 'No cars found']);
        }

        return response()->json([
            'success' => true,
            'count' => $cars->count(),
            'cars' => $cars,
        ]);
    }

    public function create()
    {
        return response()->json([
            'success' => true,
            'cars_type' => ['باص', 'سيارة', 'دراجة نارية'],
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|in:باص,سيارة,دراجة نارية',
            'model_id' => 'required|exists:models,id',
            'model_name' => 'required|string|max:255',
            'brand_id' => 'required|exists:brand',
            'registration_number' => 'required|string|max:255',
            'price_per_houre' => 'required|numeric',
            'status' => 'required|string|in:available,maintenance',
            'year' => 'required|string',
            'color' => 'required|string',
            'fuel_type' => 'required|string',
            'seats' => 'required|string',
            'doors' => 'required|string',
            'transmission' => 'required|string',
            'description' => 'nullable|string',
            'images' => 'nullable|array',
        ]);
    }
}
