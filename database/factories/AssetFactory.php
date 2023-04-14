<?php

namespace Database\Factories;

use App\Enum\CurrencyEnum;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Asset>
 */
class AssetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'currency' => $this->faker->randomElement(CurrencyEnum::cases()),
            'user_id' => User::factory(),
        ];
    }
}
