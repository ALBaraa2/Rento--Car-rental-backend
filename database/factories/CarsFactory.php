<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Models;
use App\Models\Agency;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Cars>
 */
class CarsFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'agency_id' => Agency::inRandomOrder()->first()->id ?? 1,
            'model_id' => Models::inRandomOrder()->first()->id ?? 1,
            'registration_number' => strtoupper($this->faker->bothify('???-####')),
            'price_per_day' => $this->faker->numberBetween(30, 200),
            'status' => $this->faker->randomElement(['available', 'maintenance']),
            'description' => $this->faker->sentence(10),
            'images_paths' => json_encode(['cars/car' . $this->faker->numberBetween(1, 10) . '.jpg']),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }

    public function available()
    {
        return $this->state([
            'status' => 'available',
        ]);
    }

    public function maintenance()
    {
        return $this->state([
            'status' => 'maintenance',
        ]);
    }
}
