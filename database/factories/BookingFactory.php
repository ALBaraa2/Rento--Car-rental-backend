<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\Car;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    protected $model = Booking::class;

    public function definition(): array
    {
        $startDate = $this->faker->dateTimeBetween('-6 months', '+1 month');
        $endDate = clone $startDate;
        $endDate->modify('+' . $this->faker->numberBetween(1, 14) . ' days');

        $pricePerDay = $this->faker->numberBetween(30, 200);
        $days = $endDate->diff($startDate)->days;
        $totalPrice = $pricePerDay * $days;

        return [
            'customer_id' => Customer::inRandomOrder()->first()->id ?? 1,
            'car_id' => Car::inRandomOrder()->first()->id ?? 1,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'start_time' => $this->faker->time(),
            'end_time' => $this->faker->time(),
            'total_price' => (string) $totalPrice,
            'status' => $this->faker->randomElement(['pending', 'confirmed', 'cancelled']),
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
