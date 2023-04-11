<?php

use App\Models\Asset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

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
