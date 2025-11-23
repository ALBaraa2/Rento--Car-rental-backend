<?php

namespace Database\Factories;

use App\Models\Agency;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AgencyFactory extends Factory
{
    protected $model = Agency::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->state(['role' => 'agency']),
            'commercial_register' => strtoupper(fake()->bothify('CR########')),
            'contact_email' => fake()->companyEmail(),
        ];
    }
}
