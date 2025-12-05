<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function profile()
    {
        $userId = Auth::user();

        $customer = Customer::with('user')->findOrFail($userId);

        return response()->json([
            'success' => true,
            'user' => new CustomerResource($customer)
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $customer = Customer::with('user')->where('user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'driving_license' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('profile_photos', 'public');
            $validated['photo'] = $path;
        }

        if ($request->hasFile('driving_license')) {
            $path = $request->file('driving_license')->store('driving_licenses', 'public');
            $validated['driving_license'] = $path;
        }

        $customer->user->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
        ]);

        $customer->update([
            'driving_license' => $request->driving_license ? $validated['driving_license'] : $customer->driving_license,
        ]);

        return response()->json([
            'success' => true,
            'user' => new CustomerResource($customer)
        ]);
    }
}
