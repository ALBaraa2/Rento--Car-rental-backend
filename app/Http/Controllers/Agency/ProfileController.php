<?php

namespace App\Http\Controllers\Agency;

use App\Http\Controllers\Controller;
use App\Http\Resources\AgencyResource;
use App\Models\Agency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function profile()
    {
        $user = Auth::user();

        $agency = Agency::with('user')->where("user_id", $user->id)->first();

        return response()->json([
            'success' => true,
            'agency' => new AgencyResource($agency),
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $agency = Agency::with('user')->where("user_id", $user->id)->first();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'contact_email' => 'required|email|string|max:255',
            'commercial_register' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('profile_photos', 'public');
            $validated['photo'] = $path;
        }

        if ($request->hasFile('commercial_register')) {
            $path = $request->file('commercial_register')->store('commercial_register', 'public');
            $validated['commercial_register'] = $path;
        }

        $agency->user->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
            'profile_photo_path' => $validated['photo'] ?? null,
        ]);

        $agency->update([
            'contact_email' => $validated['contact_email'],
            'commercial_register' => $validated['commercial_register'] ?? null,
        ]);

        return response()->json([
            'success' => true,
            'agency' => new AgencyResource($agency),
        ]);
    }
}
