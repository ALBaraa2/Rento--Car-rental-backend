<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Auth;

class AgencyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $user = Auth::user();
        if ($user->role == 'agency') {
            return [
                'user_id' => $this->user_id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'phone' => $this->user->phone,
                'commercial_register' => $this->commercial_register,
                'profile_image' => $this->user->profile_photo_path ? asset('storage/' . $this->user->profile_photo_path) : null,
                'commercial_register_number' => $this->commercial_register_number,
                'address' => $this->user->address,
            ];
        } else if ($user->role == 'customer') {
            return [
                'id' => $this->id,
                'user_id' => $this->user_id,
                'agency_name' => $this->user->name,
                'contact_email' => $this->contact_email,
                'phone' => $this->user->phone,
                'profile_image' => $this->user->profile_photo_path ? asset('storage/' . $this->user->profile_photo_path) : null,
                'address' => $this->user->address,
            ];
        } else {
            return [
                'user_id' => $this->user_id,
                'user_name' => $this->user->name,
            ];
        }
    }
}
