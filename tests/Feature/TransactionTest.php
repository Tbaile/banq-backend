<?php

use App\Models\Asset;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
use function Pest\Laravel\actingAs;

test('cannot create a description-empty transaction', function () {
    $user = User::factory()->create();
    $transaction = Transaction::factory()->withdrawal()->make();
    $response = $this->actingAs($user)
        ->postJson('/api/transaction', [
            'description' => '',
            'amount' => $transaction->amount,
        ]);

    $response->assertUnprocessable()
        ->assertInvalid(['description']);
});

test('cannot create an transaction with invalid amount', function (string $amount) {
    $user = User::factory()->create();
    $transaction = Transaction::factory()->withdrawal()->make();
    $response = $this->actingAs($user)
        ->postJson('/api/transaction', [
            'description' => $transaction->description,
            'amount' => $amount,
        ]);

    $response->assertUnprocessable()
        ->assertInvalid(['amount']);
})->with([
    '',
    '-1',
    '0',
    '-0.01',
]);

test('cannot create a transaction without assets', function () {
    $user = User::factory()->create();
    $transaction = Transaction::factory()->make();
    $response = $this->actingAs($user)
        ->postJson('/api/transaction', [
            'description' => $transaction->description,
            'amount' => $transaction->amount,
        ]);
    $response->assertUnprocessable()
        ->assertInvalid(['source_asset_id' => 'required', 'destination_asset_id' => 'required']);
});

test('cannot create a withdraw without source_asset', function (string $sourceAsset) {
    $user = User::factory()->create();
    $transaction = Transaction::factory()->withdrawal()->make();
    $response = $this->actingAs($user)
        ->postJson('/api/transaction', [
            'description' => $transaction->description,
            'amount' => $transaction->amount,
            'source_asset_id' => $sourceAsset,
        ]);

    $response->assertUnprocessable()
        ->assertInvalid(['source_asset_id']);
})->with([
    '',
    'wrong',
    '10',
]);

test('create a withdraw', function () {
    $user = User::factory()->create();
    $sourceAsset = Asset::factory()->recycle($user)->create();
    $transaction = Transaction::factory()->recycle($sourceAsset)->withdrawal()->make();
    $response = $this->actingAs($user)
        ->postJson('/api/transaction', [
            'description' => $transaction->description,
            'amount' => $transaction->amount,
            'source_asset_id' => $sourceAsset->id,
            'date' => $transaction->date->toAtomString(),
        ]);

    $response->assertCreated()
        ->assertJson([]);

    $this->assertDatabaseCount('transactions', 1);
    $this->assertDatabaseHas('transactions', [
        'description' => $transaction->description,
        'amount' => $transaction->amount,
        'source_asset_id' => $sourceAsset->id,
        'date' => $transaction->date,
        'latitude' => null,
        'longitude' => null,
        'address' => null,
    ]);
});

test('save transaction with location', function () {
    $transaction = Transaction::factory()->address()->withdrawal()->make();
    $user = $transaction->sourceAsset->user;
    actingAs($user)
        ->postJson('/api/transaction', [
            'description' => $transaction->description,
            'amount' => $transaction->amount,
            'source_asset_id' => $transaction->sourceAsset->id,
            'date' => $transaction->date->toAtomString(),
            'latitude' => $transaction->latitude,
            'longitude' => $transaction->longitude,
            'address' => $transaction->address,
        ])
        ->assertCreated();
    $this->assertDatabaseCount('transactions', 1);
    $this->assertDatabaseHas('transactions', [
        'description' => $transaction->description,
        'amount' => $transaction->amount,
        'source_asset_id' => $transaction->sourceAsset->id,
        'date' => $transaction->date,
        'latitude' => $transaction->latitude,
        'longitude' => $transaction->longitude,
        'address' => $transaction->address,
    ]);
});

test('list transactions for specific asset', function () {
    $user = Asset::factory()->create();
    $asset = Asset::factory()->recycle($user)->create();
    $withdrawals = Transaction::factory()->count(10)->withdrawal($asset)->create();
    $deposits = Transaction::factory()->count(10)->deposit($asset)->create();
    $transfers = Transaction::factory()->count(10)->transfer($asset, $asset)->create();
    $transactions = $withdrawals->merge($deposits)->merge($transfers);
    $firstTransaction = $transactions->sortByDesc('date')->first();

    $response = $this->actingAs($asset->user)
        ->getJson("/api/asset/{$asset->id}/transaction");
    $response
        ->assertSuccessful()
        ->assertJson(fn(AssertableJson $json) => $json
            ->count('data', 15)
            ->has('data.0', fn(AssertableJson $json) => $json
                ->where('id', $firstTransaction->id)
                ->where('description', $firstTransaction->description)
                ->where('amount', $firstTransaction->amount)
                ->where('date', $firstTransaction->date->toAtomString())
                ->where('source_asset_id', $firstTransaction->source_asset_id)
                ->where('destination_asset_id', $firstTransaction->destination_asset_id)
                ->etc()
            )->etc()
        );
});
