<?php

namespace Database\Factories;

use App\Models\Brands;
use Illuminate\Database\Eloquent\Factories\Factory;

class BrandsFactory extends Factory
{
    protected $model = Brands::class;

    public function definition(): array
    {
        $brands = [
            ['name' => 'Toyota', 'country' => 'Japan'],
            ['name' => 'Honda', 'country' => 'Japan'],
            ['name' => 'Ford', 'country' => 'USA'],
            ['name' => 'BMW', 'country' => 'Germany'],
            ['name' => 'Mercedes', 'country' => 'Germany'],
            ['name' => 'Audi', 'country' => 'Germany'],
            ['name' => 'Hyundai', 'country' => 'South Korea'],
            ['name' => 'Kia', 'country' => 'South Korea'],
            ['name' => 'Nissan', 'country' => 'Japan'],
            ['name' => 'Chevrolet', 'country' => 'USA'],
        ];

        $brand = $this->faker->randomElement($brands);

        return [
            'name' => $brand['name'],
            'country' => $brand['country'],
            'type' => $this->faker->randomElement(['باص', 'سيارة', 'دراجة نارية']),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
