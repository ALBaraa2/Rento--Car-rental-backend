<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgencyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        if (Auth::user()->role == 'agency') {
            return [
                'user_id' => $this->user_id,
                'commercial_register' => $this->commercial_register,
                'contact_email' => $this->contact_email,
                'agency_name' => $this->user->name,
            ];
        } else if (Auth::user()->role == 'customer') {
            return [
                'id' => $this->id,
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
