<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agency;
use App\Http\Resources\AgencyResource;
use App\Http\Resources\CarResource;
use App\Models\Car;

class AgenciesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $agencies = Agency::with(['user'])->activeAgencies()->approvedAgencies()->get();
        return response()->json([
            'success' => true,
            'count' => $agencies->count(),
            'agencies' => AgencyResource::collection($agencies)
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $agency = Agency::with(['user'])->findOrFail($id);

        if (!$agency->isActive() || !$agency->isApproved()) {
            return response()->json(['message' => 'Agency not Found'], 404);
        }

        return response()->json([
            'success' => true,
            'agency' => new AgencyResource($agency)
        ]);
    }

    public function search(Request $request)
    {
        $name = $request->input('name');
        $Address = $request->input('address');

        $agencies = Agency::with(['user'])->activeAgencies()->approvedAgencies()
            ->when($name, function ($query) use ($name) {
                $query->whereHas('user', function ($q) use ($name) {
                    $q->where('name', 'ilike', '%' . $name . '%');
                });
            })
            ->when($Address, function ($query) use ($Address) {
                $query->whereHas('user', function ($q) use ($Address) {
                    $q->where('address', 'ilike', '%' . $Address . '%');
                });
            })
            ->get();

        return response()->json([
            'success' => true,
            'count' => $agencies->count(),
            'agencies' => AgencyResource::collection($agencies)
        ]);
    }

    public function searchAboutCar(Request $request)
    {
        $model = $request->input('model');
        $brand = $request->input('brand');

        $cars = Car::with(['agency.user'])->activeCars()->approvedCars()
            ->when($model, function ($query) use ($model) {
                $query->where('model', 'ilike', '%' . $model . '%');
            })
            ->when($brand, function ($query) use ($brand) {
                $query->where('brand', 'ilike', '%' . $brand . '%');
            })
            ->get();

        return response()->json([
            'success' => true,
            'count' => $cars->count(),
            'cars' => CarResource::collection($cars)
        ]);
    }
}
