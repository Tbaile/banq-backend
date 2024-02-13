<?php

use App\Models\Asset;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;

test('assert can\'t view transactions for not owned asset', function () {
    $asset = Asset::factory()->create();
    $this->getJson('/api/asset/'.$asset->id.'/transaction')
        ->assertUnauthorized();
    $user = User::factory()->create();
    $this->actingAs($user)
        ->getJson('/api/asset/'.$asset->id.'/transaction')
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
        ->getJson('/api/asset/'.$asset->id.'/transaction');
    $response
        ->assertSuccessful()
        ->assertJson(fn (AssertableJson $json) => $json
            ->has('data', 9)
            ->has('data.0', fn (AssertableJson $json) => $json
                ->where('id', $latestTransaction->id)
                ->where('description', $latestTransaction->description)
                ->where('amount', $latestTransaction->amount)
                ->where('date', $latestTransaction->date->toAtomString()))
            ->has('data.8', fn (AssertableJson $json) => $json
                ->where('id', $olderTransaction->id)
                ->where('description', $olderTransaction->description)
                ->where('amount', $olderTransaction->amount)
                ->where('date', $olderTransaction->date->toAtomString()))
        );
});
