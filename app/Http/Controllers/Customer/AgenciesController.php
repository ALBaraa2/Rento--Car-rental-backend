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
        $agencies = Agency::with(['user'])->get();
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
        $agency = Agency::with(['user'])->find($id);
        if (!$agency) {
            return response()->json(['message' => 'Agency not found'], 404);
        }
        return response()->json([
            'success' => true,
            'agency' => new AgencyResource($agency)
        ]);
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
