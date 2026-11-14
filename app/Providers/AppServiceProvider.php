<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
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
        // Outside production, throw instead of silently ignoring attributes
        // that are missing from $fillable (this hid the lease "status" bug).
        Model::preventSilentlyDiscardingAttributes(! $this->app->environment('production'));
    }
}
