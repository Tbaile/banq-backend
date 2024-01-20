<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'description' => $this->faker->words(asText: true),
            'amount' => $this->faker->randomFloat(2, max: 10000),
        ];
    }

    public function withdraw(): self
    {
        return $this->state([
            'source_asset_id' => Asset::factory(),
        ]);
    }
}
