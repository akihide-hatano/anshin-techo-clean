<?php

namespace App\Providers;

use App\Models\RecordMedication;
use App\Observers\RecordMedicationObserver;
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
        // ★追加: オブザーバーを登録★
        RecordMedication::observe(RecordMedicationObserver::class);
    }
}