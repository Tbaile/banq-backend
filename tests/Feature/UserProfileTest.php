<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\getJson;

it('cannot view profile unauthenticated', function () {
    $user = User::factory()->create();
    getJson('/api/user/'.$user->id)
        ->assertUnauthorized();
});

it('view url of avatar if present', function () {
    Storage::fake('public');
    $user = User::factory()->avatar()->create();
    actingAs($user)
        ->getJson('/api/user/'.$user->id)
        ->assertSuccessful()
        ->assertJsonFragment([
            'avatar' => Storage::url($user->avatar),
        ]);
    $user = User::factory()->create();
    actingAs($user)
        ->getJson('/api/user/'.$user->id)
        ->assertSuccessful()
        ->assertJsonMissingPath('data.avatar');
});

it('update profile info', function () {
    Storage::fake('public');
    $user = User::factory()->create();
    $avatar = UploadedFile::fake()->image('avatar.jpg');
    $updatedUser = User::factory()->make();
    $response = actingAs($user)
        ->putJson('/api/user/'.$user->id, [
            'name' => $updatedUser->name,
            'email' => $updatedUser->email,
            'avatar' => $avatar,
        ])
        ->assertSuccessful();
    $user->refresh();
    $response->assertJsonFragment([
        'name' => $updatedUser->name,
        'email' => $updatedUser->email,
        'avatar' => Storage::url($user->avatar),
    ]);
    Storage::assertExists($user->avatar);
});
