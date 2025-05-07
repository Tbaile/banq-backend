<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can be created', function () {
    $user = User::factory()->make();

    $firebaseToken = fake()->uuid;
    $response = $this->postJson('/api/register', [
        'name' => $user->name,
        'email' => $user->email,
        'password' => fake()->password,
        'firebase_token' => $firebaseToken,
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
        'firebase_token' => $firebaseToken,
    ]);
});

test('firebase_token is required', function () {
    $user = User::factory()->make();

    $this->postJson('/api/register', [
        'name' => $user->name,
        'email' => $user->email,
        'password' => fake()->password,
    ])->assertInvalid([
        'firebase_token',
    ]);
});

test('no duplicate mail can be used', function () {
    $user = User::factory()->create();
    $invalidUser = User::factory()->make([
        'email' => $user->email,
    ]);

    $this->postJson('/api/register', [
        'name' => $invalidUser->name,
        'email' => $invalidUser->email,
        'password' => fake()->password,
    ])->assertInvalid([
        'email',
    ]);
});
