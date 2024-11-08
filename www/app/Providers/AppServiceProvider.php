<?php

namespace App\Providers;

use App\Helpers\Response\Answer;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind('answer', fn () => new Answer());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Route::pattern('task', '[0-9]+');
    }
}
