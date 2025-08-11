<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can login', function () {
    /** @var User $user */
    $user = User::factory()->create();
    $firebaseToken = fake()->uuid();
    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        //'email' => $user->name,
        'password' => 'password',
        'device_name' => fake()->word(),
        'firebase_token' => $firebaseToken,
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                'token',
            ],
        ]);

    $this->assertDatabaseCount('personal_access_tokens', 1);
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'firebase_token' => $firebaseToken,
    ]);
});

test('fail login', function () {
    $user = User::factory()->create();
    $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'mid_password',
        'device_name' => 'Gorgeous Device',
    ]);
    $this->postJson('/api/login', [
        'email' => 'wrongEmail@example.com',
        'password' => 'password',
        'device_name' => 'Gorgeous Device',
    ]);

    $this->assertDatabaseCount('personal_access_tokens', 0);
});

test("can't login without firebase token", function () {
    $user = User::factory()->create();
    $this->postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password',
        'device_name' => fake()->word(),
    ])->assertUnprocessable()
        ->assertInvalid(['firebase_token']);
});
