<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\CustomerResource;
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

        $customer = Customer::with('user')->findOrFail('1');

        return response()->json([
            'success' => true,
            'user' => new CustomerResource($customer)
        ]);
    }

    public function update(Request $request)
    {
        $validator = $request->validate([
            'name' => 'string|max:255',
            'email' => 'string|email|max:255|unique:users,email,' . Auth::user()->id,
            'phone' => 'string|max:255',
            'driven_license' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'password' => 'string|min:8|max:255',
        ]);

        $userId = Auth::user()->id;

        $customer = Customer::findOrFail($userId);

        $customer->update($request->all());

        return response()->json([
            'success' => true,
            'user' => new CustomerResource($customer)
        ]);
    }
}
