<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Models\Agency;
use App\Models\Brands;
use App\Models\Car;
use App\Models\Models;
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

        $cars = $agency->cars()
            ->with(['model.brand'])
            ->withAvg('reviews as reviews_avg_rating', 'rating')
            ->get();

        if ($cars->count() == 0) {
            return response()->json(['message'=> 'No cars found']);
        }

        return response()->json([
            'success' => true,
            'count' => $cars->count(),
            'cars' => $cars,
        ]);
    }

    public function getTypes()
    {
        return response()->json([
            'success' => true,
            'cars_type' => ['باص', 'سيارة', 'دراجة نارية'],
        ]);
    }

    public function getBrandName(Request $request)
    {
        $brand = Brands::where('type', $request->type)->get();

        return response()->json([
            'success' => true,
            'brand' => $brand,
        ]);
    }

    public function getModels(Request $request)
    {
        $models = Models::whereHas('brand', function ($q) use ($request) {
            $q->where('type', $request->type);
        })->where('brand_id', $request->brandId)->get();

        return response()->json([
            'success' => true,
            'models' => $models,
        ]);
    }

    public function getTransmission()
    {
        return response()->json([
            'success' => true,
            'transmission' => ['automatic', 'manual'],
        ]);
    }

    public function getColor()
    {
        return response()->json([
            'success' => true,
            'color' => ['أبيض',
                'أسود',
                'فضي',
                'رمادي',
                'أزرق',
                'أحمر',
                'بني',
                'بيج',
                'أخضر',
                'أصفر',
                'برتقالي',
                'ذهبي',
                'برونزي',
                'أبيض لؤلؤي',
                'أزرق معدني',
                'أسود مطفي',
                'شامبين',
                'خمري',
                'أخضر غامق',
                'أزرق كحلي',
            ],
        ]);
    }

    public function getFuelType()
    {
        return response()->json([
            'success'=> true,
            'fuel_type' => ['بنزين',
                'ديزل',
                'هايبرد',
                'كهرباء',
                'غاز',
                'بنزين + كهرباء (Hybrid)',
                'ديزل + كهرباء (Mild Hybrid)'
            ],
        ]);
    }

    public function store(Request $request)
    {

        if (Auth::user()->role != 'agency') {
            return response()->json([
                'success'=> false,
                'message'=> 'Unauthorized'
            ]);
        }

        $validated = $request->validate([
            'type' => 'required|in:باص,سيارة,دراجة نارية',
            'model_id' => 'required|exists:models,id',
            'registration_number' => 'required|string|max:255',
            'price_per_hour' => 'required|numeric',
            'status' => 'required|string|in:available,maintenance',
            'color' => 'required|string',
            'fuel_type' => 'required|string',
            'seats' => 'required|string',
            'doors' => 'required|string',
            'transmission' => 'required|string',
            'description' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $car = Car::create([
            'agency_id' => $request->user()->agency->id,
            'type' => $validated['type'],
            'model_id' => $validated['model_id'],
            'registration_number' => $validated['registration_number'],
            'price_per_hour' => $validated['price_per_hour'],
            'status' => $validated['status'],
            'color' => $validated['color'],
            'fuel_type' => $validated['fuel_type'],
            'seats' => $validated['seats'],
            'doors' => $validated['doors'],
            'transmission' => $validated['transmission'],
            'description' => $validated['description'],
        ]);

        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('cars', 'public');
                $images[] = $path;
            }
            $car->images_paths = json_encode($images);
            $car->save();
        }

        return response()->json([
            'success' => true,
            'car' => $car,
            'message' => 'Car created successfully',
        ]);
    }
}
