<?php

use App\Models\User;

test('cannot get user without token')
    ->getJson('/api/user/me')
    ->assertUnauthorized();

test('get user identity', function () {
    $user = User::factory()->create();
    $this->actingAs($user)
        ->getJson('/api/user/me')
        ->assertJson([
            'data' => [
                'name' => $user->name,
                'email' => $user->email
            ]
        ]);
});
