<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
use App\Http\Resources\UserResource;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'driving_license' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ]);

        if ($request->hasFile('driving_license')) {
            $path = $request->file('driving_license')->store('driving_licenses', 'public');
            $validated['driving_license'] = $path;
        }

        $customer->user->update([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        $customer->update([
            'driving_license' => $request->driving_license ? $request->driving_license->store('driving_licenses', 'public') : $customer->driving_license,
        ]);

        return response()->json([
            'success' => true,
            'user' => new CustomerResource($customer)
        ]);
    }
}
