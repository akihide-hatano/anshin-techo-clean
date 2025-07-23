<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

class PushNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $title;
    protected $body;
    protected $url; // 通知クリック時に開くURLを追加

    /**
     * Create a new notification instance.
     *
     * @param string $title
     * @param string $body
     * @param string|null $url
     * @return void
     */
    public function __construct(string $title, string $body, ?string $url = null)
    {
        $this->title = $title;
        $this->body = $body;
        $this->url = $url;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['fcm']; // FCMチャネルを使用することを指定
    }

    /**
     * Get the Fcm representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Kreait\Firebase\Messaging\CloudMessage
     */
    public function toFcm($notifiable)
    {
        $notification = FirebaseNotification::create($this->title, $this->body);

        $message = CloudMessage::new();
        $message = $message->withNotification($notification);

        // URLが指定されていれば、dataペイロードに追加
        // Service Workerでこのdataを拾って開く
        if ($this->url) {
            $message = $message->withData([
                'click_action' => $this->url,
            ]);
        }

        return $message;
    }
}