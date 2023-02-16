<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can be created', function () {
    $user = User::factory()->make();

    $response = $this->postJson('/api/register', [
        'name' => $user->name,
        'email' => $user->email,
        'password' => fake()->password,
    ]);

    $response->assertCreated()
        ->assertJson([
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);

    $this->assertDatabaseHas(User::class, [
        'name' => $user->name,
        'email' => $user->email,
    ]);
});

test('no duplicate mail can be used', function () {
    $user = User::factory()->create();
    $invalidUser = User::factory()->make([
        'email' => $user->email
    ]);

    $this->postJson('/api/register', [
        'name' => $invalidUser->name,
        'email' => $invalidUser->email,
        'password' => fake()->password,
    ])->assertInvalid([
        'email'
    ]);
});
