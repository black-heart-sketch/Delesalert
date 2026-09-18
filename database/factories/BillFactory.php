<?php

namespace Database\Factories;

use App\Models\Bill;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bill>
 */
class BillFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'provider_name' => 'ENEO Cameroon',
            'account_reference' => 'DLST-'.fake()->unique()->numerify('######'),
            'description' => fake()->monthName().' electricity usage',
            'amount_due' => fake()->numberBetween(5000, 50000),
            'currency' => 'XAF',
            'due_date' => fake()->dateTimeBetween('now', '+30 days'),
            'status' => 'UNPAID',
        ];
    }
}
