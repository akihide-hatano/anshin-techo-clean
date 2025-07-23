<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Notification;
use Kreait\Laravel\Firebase\Firebase; // 必要なuseステートメント
use Kreait\Firebase\Messaging\CloudMessage; // 必要なuseステートメント
use Illuminate\Notifications\Channels\Channel; // 必要なuseステートメント
use Illuminate\Support\Facades\Log; // 必要なuseステートメント
use App\Notifications\PushNotification; // ★★★ この行を追加 ★★★ (toFcmエラー解消のため)
use Illuminate\Notifications\Notification as BaseNotification; // LaravelのNotificationクラスと名前衝突を避けるため

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
                 * @param  \Illuminate\Notifications\Notification  $notification // この行はコメントアウトまたは削除
                 * @return void
                 */
                // ★★★ ここを修正 ★★★
                public function send($notifiable, BaseNotification $notification) // BaseNotificationのままにして、toFcmのチェックを追加
                {
                    // sendメソッドに渡される$notificationがPushNotificationのインスタンスであることを期待
                    // toFcmメソッドはPushNotificationクラスに存在するため、型チェックを行う
                    if (!($notification instanceof PushNotification)) {
                        Log::warning('Notification is not an instance of PushNotification. Cannot call toFcm method.');
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
    }
}