<?php

namespace App\Providers;

use App\Http\Middleware\TeamsPermissionMiddleware;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Http\Kernel;
use Illuminate\Routing\Middleware\SubstituteBindings;

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
        Vite::prefetch(concurrency: 3);

        $kernel = app()->make(Kernel::class);

        $kernel->addToMiddlewarePriorityBefore(
            SubstituteBindings::class,
            TeamsPermissionMiddleware::class,
        );
    }
}
