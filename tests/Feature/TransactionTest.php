<?php

use App\Models\Asset;
use App\Models\Transaction;
use App\Models\User;

test('cannot create a description-empty transaction', function () {
    $user = User::factory()->create();
    $transaction = Transaction::factory()->withdraw()->make();
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
    $transaction = Transaction::factory()->withdraw()->make();
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
    $transaction = Transaction::factory()->withdraw()->make();
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
    $transaction = Transaction::factory()->recycle($sourceAsset)->withdraw()->make();
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
    ]);
});
