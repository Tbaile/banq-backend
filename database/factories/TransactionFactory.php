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
            'date' => $this->faker->dateTimeThisMonth(),
        ];
    }

    public function withdrawal(): self
    {
        return $this->state([
            'source_asset_id' => Asset::factory(),
        ]);
    }

    public function deposit(): self
    {
        return $this->state([
            'destination_asset_id' => Asset::factory(),
        ]);
    }

    public function transfer(): self
    {
        return $this->state([
            'source_asset_id' => Asset::factory(),
            'destination_asset_id' => Asset::factory(),
        ]);
    }
}
