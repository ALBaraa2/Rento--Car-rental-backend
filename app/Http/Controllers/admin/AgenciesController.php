<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AgencyRequest;
use App\Http\Resources\UserResource;
use App\Models\Agency;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AgenciesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $agencies = Agency::with('user')->get();

        return view('admin.agency.index')->with('agencies', $agencies);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AgencyRequest $request)
    {
        $validated = $request->validate();

        if ($request->hasFile('commercial_register')) {
            $path = $request->file('commercial_register')->store('commercial_registers', 'public');
            $validated['commercial_register'] = $path;
        }

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('profile_photos', 'public');
            $validated['photo'] = $path;
        }

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'agency',
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'profile_photo_path' => $validated['photo'] ?? null,
            'is_approved' => false,
        ]);

        Agency::create([
            'user_id' => $user->id,
            'commercial_register' => $validated['commercial_register'] ?? null,
            'commercial_register_number' => $validated['commercial_register_number'] ?? null,
        ]);

        return view('admin.agency.index')->with('message', 'Agency created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
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
