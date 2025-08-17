<?php

namespace App\Providers;

use App\Models\RecordMedication;
use App\Observers\RecordMedicationObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        RecordMedication::observe(RecordMedicationObserver::class);

        if (app()->environment('production')) {
        URL::forceScheme('https');
        }
    }
}