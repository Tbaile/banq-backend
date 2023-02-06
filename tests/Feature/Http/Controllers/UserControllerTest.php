<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can be created', function () {
    $user = User::factory()->make();

    $response = $this->postJson('/api/user', [
        'name' => $user->name,
        'email' => $user->email,
        'password' => fake()->password,
    ]);

    $response->assertCreated()
        ->assertJsonFragment([
            'name' => $user->name,
            'email' => $user->email,
        ]);

    $this->assertDatabaseHas(User::class, [
        'name' => $user->name,
        'email' => $user->email,
    ]);
});
