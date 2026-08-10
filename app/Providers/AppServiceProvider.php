<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Feature services are resolved through constructor injection.
    }

    public function boot(): void
    {
        // No production-specific bootstrapping is included in this sample.
    }
}
