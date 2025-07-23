<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Notification;
use Kreait\Laravel\Firebase\Firebase;
use Kreait\Firebase\Messaging\CloudMessage;
use Illuminate\Notifications\Channels\Channel;
use Illuminate\Support\Facades\Log;
use Illuminate\Notifications\Notification as BaseNotification;

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
    public function boot(): void // ★★★ この行を追加（抜けていました） ★★★
    {
        // FCM通知チャネルを登録
        Notification::extend('fcm', function ($app) {
            return new class($app->make(Firebase::class)->messaging()) extends Channel {
                protected $messaging;

                public function __construct(\Kreait\Firebase\Messaging $messaging)
                {
                    $this->messaging = $messaging;
                }

                /**
                 * Send the given notification.
                 *
                 * @param  mixed  $notifiable
                 * @param  \Illuminate\Notifications\Notification  $notification
                 * @return void
                 */
                public function send($notifiable, BaseNotification $notification)
                {
                    if (! method_exists($notification, 'toFcm')) {
                        Log::warning('Notification does not have a toFcm method.');
                        return;
                    }

                    if (! $message = $notification->toFcm($notifiable)) {
                        return;
                    }

                    try {
                        $this->messaging->send($message);
                        Log::info('FCM notification sent successfully.');
                    } catch (\Throwable $e) {
                        Log::error('FCM notification failed: ' . $e->getMessage());
                    }
                }
            };
        });
    } // ★★★ この閉じ括弧は、追加した public function boot(): void に対応します ★★★
}