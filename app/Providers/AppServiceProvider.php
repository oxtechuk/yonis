<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Fix MySQL "Specified key was too long" error with utf8mb4
        // utf8mb4 uses 4 bytes/char, so 191 * 4 = 764 bytes < 1000 byte limit
        Schema::defaultStringLength(191);
    }
}
