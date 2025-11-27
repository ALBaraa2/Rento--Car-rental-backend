<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Agency;
use App\Http\Resources\AgencyResource;

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

    public function searchAboutAgency(Request $request)
    {
        $name = $request->input('name');
        $Address = $request->input('address');

        $agencies = Agency::with(['user'])->where('is_active', true)->where('is_approved', true)
            ->when($name, function ($query) use ($name) {
                $query->where('name', 'ilike', '%' . $name . '%');
            })
            ->when($Address, function ($query) use ($Address) {
                $query->where('address', 'ilike', '%' . $Address . '%');
            })
            ->get();

        return response()->json([
            'success' => true,
            'count' => $agencies->count(),
            'agencies' => AgencyResource::collection($agencies)
        ]);
    }
}
