<?php

namespace Database\Factories;

use App\Models\Models;
use App\Models\Brands;
use Illuminate\Database\Eloquent\Factories\Factory;

class ModelsFactory extends Factory
{
    public function definition(): array
    {
        $models = [
            'Toyota' => ['Camry', 'Corolla', 'RAV4', 'Hilux', 'Prius'],
            'Honda' => ['Civic', 'Accord', 'CR-V', 'HR-V', 'Pilot'],
            'Ford' => ['Focus', 'Fiesta', 'Mustang', 'Explorer', 'Ranger'],
            'BMW' => ['X5', 'X3', '3 Series', '5 Series', '7 Series'],
            'Mercedes' => ['C-Class', 'E-Class', 'S-Class', 'GLC', 'GLE'],
        ];

        $brandName = $this->faker->randomElement(array_keys($models));
        $modelName = $this->faker->randomElement($models[$brandName]);

        return [
            'brand_id' => Brands::where('name', $brandName)->first()->id ?? 1,
            'name' => $modelName,
            'year' => $this->faker->numberBetween(2018, 2024),
            'type' => $this->faker->randomElement(['باص', 'سيارة', 'دراجة نارية']),
            'color' => $this->faker->colorName(),
            'fuel_type' => $this->faker->randomElement(['petrol', 'diesel', 'electric', 'hybrid']),
            'seats' => $this->faker->numberBetween(2, 8),
            'doors' => $this->faker->numberBetween(2, 5),
            'transmission' => $this->faker->randomElement(['automatic', 'manual']),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
