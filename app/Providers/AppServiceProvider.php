<?php

namespace App\Providers;

use App\Services\Workshop\WorkshopContent;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(WorkshopContent::class, fn () => new WorkshopContent);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Dates affichees en francais dans tout le module (ex. "lundi 2 septembre").
        Carbon::setLocale('fr');
        CarbonImmutable::setLocale('fr');
    }
}
