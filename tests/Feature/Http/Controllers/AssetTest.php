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
    User::factory()->hasAssets(10)->create();
    $this->actingAs($user)
        ->getJson('/api/asset')
        ->assertSuccessful()
        ->assertJson(fn (AssertableJson $json) => $json
            ->has('data', 5, fn (AssertableJson $json) => $json
                ->where('id', $asset->id)
                ->where('name', $asset->name)
                ->where('currency', $asset->currency->value)
                ->where('balance', 0))
            ->etc());
});

test('can show balance of asset', function () {
    $user = User::factory()->create();
    Asset::factory()
        ->recycle($user)
        ->forUser()
        ->hasOutcome(5, [
            'amount' => 100,
        ])->hasIncome(5, [
            'amount' => 20,
        ])->create();

    $this->actingAs($user)
        ->getJson('/api/asset')
        ->assertSuccessful()
        ->assertJson(fn (AssertableJson $json) => $json
            ->has('data', 1, fn (AssertableJson $json) => $json
                ->where('balance', -400)
                ->etc())
            ->etc());
});

test('cannot show not owned assets', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->hasAssets()->create();
    $this->actingAs($user)
        ->getJson('/api/asset/'.$otherUser->assets->first()->id)
        ->assertForbidden();
});

test('cannot show asset to unauthenticated', function () {
    $asset = Asset::factory()->create();
    $this->getJson('/api/asset/'.$asset->id)
        ->assertUnauthorized();
});

test('show asset', function () {
    $user = User::factory()->create();
    $asset = Asset::factory()->recycle($user)->hasIncome(4, [
        'amount' => 100,
    ])->hasOutcome(2, [
        'amount' => 20,
    ])->create();
    $response = $this->actingAs($user)->getJson('/api/asset/'.$asset->id);
    $response->assertSuccessful()
        ->assertJson(fn (AssertableJson $json) => $json
            ->has('data', fn (AssertableJson $json) => $json
                ->where('id', $asset->id)
                ->where('name', $asset->name)
                ->where('currency', $asset->currency->value)
                ->where('balance', 360)
                ->has('transactions', 6)
            )
        );
});

test('assert can\'t view transactions for not owned asset', function () {
    $asset = Asset::factory()->create();
    $this->getJson('/api/asset/'.$asset->id)
        ->assertUnauthorized();
    $user = User::factory()->create();
    $this->actingAs($user)
        ->getJson('/api/asset/'.$asset->id)
        ->assertForbidden();
});

it('returns existing transactions', function () {
    $asset = Asset::factory()
        ->hasIncome(3)
        ->hasOutcome(6)
        ->create();

    $latestTransaction = $asset->transactions()->orderByDesc('date')->orderByDesc('id')->first();
    $olderTransaction = $asset->transactions()->orderBy('date')->orderBy('id')->first();

    $response = $this->actingAs($asset->user)
        ->getJson('/api/asset/'.$asset->id);
    $response
        ->assertSuccessful()
        ->assertJson(fn (AssertableJson $json) => $json
            ->has('data.transactions', 9)
            ->has('data.transactions.0', fn (AssertableJson $json) => $json
                ->where('id', $latestTransaction->id)
                ->where('description', $latestTransaction->description)
                ->where('amount', $latestTransaction->amount)
                ->where('date', $latestTransaction->date->toAtomString()))
            ->has('data.transactions.8', fn (AssertableJson $json) => $json
                ->where('id', $olderTransaction->id)
                ->where('description', $olderTransaction->description)
                ->where('amount', $olderTransaction->amount)
                ->where('date', $olderTransaction->date->toAtomString()))
        );
});
