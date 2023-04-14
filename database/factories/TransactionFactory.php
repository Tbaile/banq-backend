<?php

namespace Database\Factories;

use App\Enum\TransactionType;
use App\Models\Asset;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Transaction>
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
            'type' => TransactionType::WITHDRAWAL,
            'source_asset_id' => Asset::factory(),
        ]);
    }
}
