<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\travel;

it('updates only the latest_activity fields on user', function () {
    $user = User::factory()->create();
    travel(1)->day(function () use ($user) {
        $updatedAt = $user->updated_at;
        $latestActivity = $user->latest_activity;
        actingAs($user)
            ->get('/api/asset');
        $user->fresh();
        expect($user->updated_at->toAtomString())->toBe($updatedAt->toAtomString())
            ->and($user->latest_activity->toAtomString())->toBe($latestActivity->addDay()->toAtomString());
    });
});
