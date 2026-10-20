<?php

namespace Database\Factories;

use App\Enums\Trade;
use App\Models\Contractor;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contractor>
 */
class ContractorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->company(),
            'trade' => fake()->randomElement(Trade::cases()),
            'email' => fake()->unique()->companyEmail(),
            'phone' => fake()->optional(0.8)->phoneNumber(),
        ];
    }

    /**
     * Indicate the contractor's trade.
     */
    public function trade(Trade $trade): static
    {
        return $this->state(fn (array $attributes) => [
            'trade' => $trade,
        ]);
    }
}
