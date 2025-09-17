<?php

namespace App\Providers;

use App\Services\DiscordAttendanceService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(DiscordAttendanceService::class, function ($app) {
            return new DiscordAttendanceService();
        });
    }

    public function boot(): void
    {
        //
    }
}
