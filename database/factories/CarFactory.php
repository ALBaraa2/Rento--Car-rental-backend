<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Models;
use App\Models\Agency;
use App\Models\Car;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Car>
 */
class CarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    protected $model = Car::class;

    public function definition(): array
    {
        return [
            'agency_id' => Agency::inRandomOrder()->value('id') ?? 1,
            'model_id' => Models::inRandomOrder()->value('id') ?? 1,

            'registration_number' => strtoupper($this->faker->bothify('???-####')),
            'price_per_hour' => $this->faker->numberBetween(30, 200),

            // moved from models table
            'color' => $this->faker->colorName(),
            'fuel_type' => $this->faker->randomElement(['petrol', 'diesel', 'electric', 'hybrid']),
            'seats' => $this->faker->numberBetween(2, 8),
            'doors' => $this->faker->numberBetween(2, 5),
            'transmission' => $this->faker->randomElement(['automatic', 'manual']),

            'status' => $this->faker->randomElement(['available', 'maintenance']),
            'description' => $this->faker->sentence(10),
            'images_paths' => ['cars/car' . $this->faker->numberBetween(1, 10) . '.jpg'],

            'created_at' => now(),
            'updated_at' => now(),
            'is_featured' => $this->faker->boolean(),
        ];
    }

    public function available()
    {
        return $this->state(['status' => 'available']);
    }

    public function maintenance()
    {
        return $this->state(['status' => 'maintenance']);
    }
}
