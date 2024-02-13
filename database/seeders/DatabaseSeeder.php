<?php

namespace Database\Seeders;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // application user
        $user = User::factory()->create(['email' => 'admin@banq.com']);
        // some random assets with income/outcome
        Asset::factory()->count(3)->for($user)
            ->hasOutcome(5)
            ->hasIncome(10)
            ->create();
    }
}
