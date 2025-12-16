<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Customer;
use App\Models\Agency;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function registerCustomer(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'address' => ['nullable', 'string', 'max:255'],
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

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'customer',
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'profile_photo_path' => $validated['photo'] ?? null,
            'is_approved' => false,
        ]);

        Customer::create([
            'user_id' => $user->id,
            'driving_license' => $validated['driving_license'] ?? null,
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return (new UserResource($user))->additional(['token' => $token,
        'token_type' => 'Bearer',
        'expires_at' => now()->addHours(1)->toISOString()])
        ->response()->setStatusCode(201);
    }

    public function registerAgency(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone' => ['nullable', 'string', 'max:20'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'address' => ['nullable', 'string', 'max:255'],
            'commercial_register' => ['nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
            'commercial_register_number' => ['nullable', 'string'],
        ]);

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

        $token = $user->createToken('auth_token')->plainTextToken;

        return (new UserResource($user))->additional(['token' => $token,
        'token_type' => 'Bearer',
        'expires_at' => now()->addHours(1)->toISOString()])
        ->response()->setStatusCode(201);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (! $user || ! Hash::check($validated['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (! $user->is_active) {
            return response()->json(['message' => 'Account disabled, Contact admin'], 403);
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return (new UserResource($user))->additional([
            'role' => $user->role,
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_at' => now()->addHours(1)->toISOString()
        ])->response()->setStatusCode(200);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    }

    public function refresh(Request $request)
    {
        try {
            $user = $request->user();

            $user->currentAccessToken()->delete();

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'token' => $token,
                'token_type' => 'Bearer',
                'expires_in' => config('sanctum.expiration', 525600)
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Token refresh failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
