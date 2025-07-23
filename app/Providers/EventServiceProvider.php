<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\MedicationMarkedUncompleted; // ★この行を追加★
use App\Listeners\SendAdminNotification;    // ★この行を追加★

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
    Registered::class => [
        SendEmailVerificationNotification::class,
    ],
    // ★★★ ここにMedicationMarkedUncompletedイベントとSendAdminNotificationリスナーのマッピングを追加 ★★★
    MedicationMarkedUncompleted::class => [
        SendAdminNotification::class,
    ],
    ];
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
