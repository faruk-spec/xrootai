<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if (class_exists(\Illuminate\Foundation\AliasLoader::class) && class_exists(\Laravel\Socialite\Facades\Socialite::class)) {
            \Illuminate\Foundation\AliasLoader::getInstance()->alias('Socialite', \Laravel\Socialite\Facades\Socialite::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
