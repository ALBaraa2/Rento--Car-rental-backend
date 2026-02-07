<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\CarResource;
use App\Models\Agency;
use Illuminate\Http\Request;
use App\Models\Car;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $featuredCars = Car::featured()->get();
        $bestCars = Car::with('agency.user')->bestRated()->take(5)->get();
        $mostBookedBrands = Car::with('model.brand')->mostBookedBrandName();

        return response()->json([
            'success' => true,
            'featuredCars' => CarResource::collection($featuredCars),
            'bestCars' => CarResource::collection($bestCars),
            'mostBookedBrands' => $mostBookedBrands,
        ]);
    }

    public function search(Request $request)
    {
        $model = $request->input('model');
        $brand = $request->input('brand');
        $type = $request->input('type');
        $min_price = $request->input('min_price');
        $max_price = $request->input('max_price');
        $rating = $request->input('rating');

        $cars = Car::with(['agency.user', 'model.brand', 'reviews'])->Available()->withAvg('reviews as avarage_rating', 'rating')
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
