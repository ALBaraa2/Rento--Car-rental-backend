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

    public function search(Request $request)
    {
        $brand = $request->input('brand');
        $model = $request->input('model');
        $rating = $request->input('rating');

        $agency = Auth::user()->agency;

        if (!$agency) {
            return response()->json(['message' => 'Agency not found'], 404);
        }

        $query = $agency->cars()->with(['model.brand']);

        if ($brand) {
            $query->whereHas('model.brand', function ($q) use ($brand) {
                $q->where('name', 'ilike', "%{$brand}%");
            });
        }

        if ($model) {
            $query->whereHas('model', function ($q) use ($model) {
                $q->where('name', 'ilike', "%{$model}%");
            });
        }

        if ($rating) {
            $query->when($rating, function ($query) use ($rating) {
                $query->whereRaw('(select avg(rating) from reviews where reviews.car_id = cars.id) >= ?', [$rating]);
            });
        } else {
            $query->withAvg('reviews as reviews_avg_rating', 'rating');
        }

        $cars = $query->get();

        if ($cars->isEmpty()) {
            return response()->json(['message' => 'No cars found'], 404);
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

    public function getStatus()
    {
        return response()->json([
            'success'=> true,
            'status' => ['available', 'maintenance'],
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
            'model_id' => 'required|exists:models,id',
            'registration_number' => 'nullable|string|max:255',
            'price_per_hour' => 'nullable|numeric',
            'status' => 'required|string|in:available,maintenance',
            'color' => 'nullable|string',
            'fuel_type' => 'nullable|string',
            'seats' => 'nullable|string',
            'doors' => 'nullable|string',
            'transmission' => 'nullable|string',
            'description' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $car = Car::create([
            'agency_id' => $request->user()->agency->id,
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

    public function softdelete(string $id)
    {
        $car = Car::with(['bookings', 'agency'])->findOrFail($id);

        if ($car->agency_id !== Auth::user()->agency->id) {
            return response()->json([
                'success' => false,
                'message' => 'You are not authorized to delete this car',
            ], 403);
        }

        if ($car->bookings->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Car has active bookings, cannot be deleted',
            ]);
        }

        $car->delete();

        return response()->json([
            'success' => true,
            'message' => 'Car deleted successfully',
        ]);
    }
}
