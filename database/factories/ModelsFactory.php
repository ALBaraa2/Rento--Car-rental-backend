<?php

namespace Database\Factories;

use App\Models\Models;
use App\Models\Brands;
use Illuminate\Database\Eloquent\Factories\Factory;

class ModelsFactory extends Factory
{
    protected $model = Models::class;

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

        $brand = Brands::where('name', $brandName)->first();

        return [
            'brand_id' => $brand?->id ?? Brands::inRandomOrder()->value('id') ?? 1,
            'name' => $modelName,
            'year' => $this->faker->numberBetween(2018, 2024),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
