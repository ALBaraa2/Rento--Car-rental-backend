<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CarRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'model_id' => 'required|exists:models,id',
            'registration_number' => 'nullable|string|max:255',
            'price_per_hour' => 'nullable|numeric',
            'status' => 'required|string|in:available,maintenance',
            'color' => 'nullable|string',
            'fuel_type' => 'nullable|string',
            'seats' => 'nullable|string',
            'doors' => 'nullable|string',
            'transmission' => 'nullable|string',
            'description' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];
    }
}
