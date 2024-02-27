<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Spatie\Health\Checks\Checks\DatabaseCheck;
use Spatie\Health\Facades\Health;

class HealthProvider extends ServiceProvider
{
    /**
     * Register health checks.
     */
    public function register(): void
    {
        Health::checks([
            DatabaseCheck::new(),
        ]);
    }
}
