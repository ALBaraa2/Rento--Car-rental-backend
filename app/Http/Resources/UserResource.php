<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if ($this->role == 'agency') {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'role' => $this->role,
                'is_active' => $this->is_active,
                'is_approved' => $this->is_approved,
                'profile_photo_path' => $this->profile_photo_path,
                'address' => $this->address,
                'commercial_register' => $this->agency->commercial_register ? asset('storage/' . $this->agency->commercial_register) : null,
                'commercial_register_number' => $this->agency->commercial_register_number,
            ];
        } elseif ($this->role == 'customer') {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'role' => $this->role,
                'is_active' => $this->is_active,
                'is_approved' => $this->is_approved,
                'profile_photo_path' => $this->profile_photo_path,
                'address' => $this->address,
                'driving_license' => $this->customer->driving_license ? asset('storage/' . $this->customer->driving_license) : null,
            ];
        }
        return [];
    }
}
