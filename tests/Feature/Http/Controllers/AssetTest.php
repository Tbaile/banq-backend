<?php

use App\Models\Asset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;

uses(RefreshDatabase::class);

test('cannot create a asset without proper input', function (string $name, string $enum) {
    $user = User::factory()->create();
    $this->actingAs($user)
        ->postJson('/api/asset', [
            'name' => $name,
            'currency' => $enum,
        ])->assertUnprocessable();
})->with([
    '',
])->with([
    '',
    'ENG',
    'E',
]);

test('cannot create asset without authentication', function () {
    $this->postJson('/api/asset')
        ->assertUnauthorized();
});

test('can create assets', function () {
    $asset = Asset::factory()->make();
    $user = User::factory()->create();
    $this->actingAs($user)
        ->postJson('/api/asset', [
            'name' => $asset->name,
            'currency' => $asset->currency,
        ])->assertCreated()
        ->assertJson([
            'data' => [
                'name' => $asset->name,
                'currency' => $asset->currency->value,
            ],
        ]);
    $this->assertDatabaseHas('assets', [
        'name' => $asset->name,
        'currency' => $asset->currency->value,
    ]);
    $this->assertDatabaseCount('assets', 1);
});

test('can list own assets', function () {
    $user = User::factory()->hasAssets(5)->create();
    $asset = $user->assets->first();
    $otherUser = User::factory()->hasAssets(10)->create();
    $this->actingAs($user)
        ->getJson('/api/asset')
        ->assertSuccessful()
        ->assertJson(fn (AssertableJson $json) => $json
            ->has('data', 5, fn (AssertableJson $json) => $json
                ->where('id', $asset->id)
                ->where('name', $asset->name)
                ->where('currency', $asset->currency->value))
            ->etc());
});
