<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('user can login', function () {
    /** @var User $user */
    $user = User::factory()->create();
    $response = $this->postJson('/api/login', [
        'email' => $user->email,
        //'email' => $user->name,
        'password' => 'password',
        'device_name' => fake()->word(),
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'data' => [
                'token',
            ],
        ]);

    $this->assertDatabaseCount('personal_access_tokens', 1);
});

test('fail login', function (string $email) {
    /** @var User $user */
    User::factory()->create([
        'email' => 'mysecureemail@example.com',
    ]);
    $response = $this->postJson('/api/login', [
        'email' => $email,
        'password' => 'mid_password',
        'device_name' => 'Gorgeous Device',
    ]);

    $response->assertUnprocessable()
        ->assertInvalid(['email']);
    $this->assertDatabaseCount('personal_access_tokens', 0);
})->with([
    'mysecureemail@example.com',
    'notsogoodmail@example.com',
]);
