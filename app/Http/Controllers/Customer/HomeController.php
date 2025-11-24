<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
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
        $mostBookedCars = Car::with('model.brand')->mostBooked()->get();

        return response()->json([
            'featuredCars' => $featuredCars,
            'bestCars' => $bestCars,
            'mostBookedBrands' => $mostBookedBrands,
            'mostBookedCars' => $mostBookedCars,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
