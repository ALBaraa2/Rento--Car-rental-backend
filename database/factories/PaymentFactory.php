<?php

namespace Database\Factories;

use App\Models\Booking;
use App\Models\Customer;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    public function definition(): array
    {
        $status = $this->faker->randomElement(['initiated', 'authorized', 'captured', 'failed']);

        return [
            'booking_id' => Booking::inRandomOrder()->first()->id ?? 1,
            'customer_id' => Customer::inRandomOrder()->first()->id ?? 1,
            'amount' => $this->faker->numberBetween(50, 1000),
            'currency' => 'USD',
            'provider' => $this->faker->randomElement(['stripe', 'paypal']),
            'payment_method' => $this->faker->randomElement(['credit_card', 'debit_card', 'digital_wallet']),
            'status' => $status,
            'transaction_id' => 'txn_' . Str::random(10),
            'receipt_url' => $this->faker->url(),
            'initiated_at' => now()->subHours($this->faker->numberBetween(1, 24)),
            'captured_at' => $status === 'captured' ? now()->subHours($this->faker->numberBetween(1, 12)) : null,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
