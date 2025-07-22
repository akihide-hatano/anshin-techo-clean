    <?php

    namespace App\Listeners;

    use App\Events\MedicationMarkedUncompleted;
    use App\Models\User;
    use App\Models\FcmToken;
    use Illuminate\Contracts\Queue\ShouldQueue;
    use Illuminate\Queue\InteractsWithQueue;
    use Illuminate\Support\Facades\Log;
    use Kreait\Firebase\Messaging\CloudMessage;
    use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
    use Kreait\Firebase\Factory;

    class SendAdminNotification implements ShouldQueue
    {
        use InteractsWithQueue;

        protected $firebaseFactory;

        /**
         * Create the event listener.
         */
        public function __construct(Factory $firebaseFactory)
        {
            $this->firebaseFactory = $firebaseFactory;
        }

        /**
         * Handle the event.
         */
        public function handle(MedicationMarkedUncompleted $event): void
        {
            Log::info('MedicationMarkedUncompleted event received. Sending admin notification via FCM.');

            try {
                $messaging = $this->firebaseFactory->createMessaging();
            } catch (\Exception $e) {
                Log::error('Firebase Messaging creation error in admin listener: ' . $e->getMessage());
                return;
            }

            // 'admin' ロールを持つユーザーのFCMトークンを取得します。
            $adminTokens = FcmToken::whereHas('user', function ($query) {
                $query->where('role', 'admin'); // 'admin' ロールを持つユーザーをフィルタリング
            })->pluck('token')->toArray();

            if (empty($adminTokens)) {
                Log::warning('No FCM tokens found for admin users. Admin notification skipped.');
                return;
            }

            $notificationTitle = '内服未完了アラート';
            $notificationBody = "ユーザー: {$event->user->name} が「{$event->medication->medication_name}」を未完了にしました。";
            if ($event->reason) {
                $notificationBody .= " 理由: {$event->reason}";
            }
            $notificationBody .= " (記録ID: {$event->record->record_id})";

            try {
                $notification = FirebaseNotification::create($notificationTitle, $notificationBody);

                $message = CloudMessage::new()
                    ->withNotification($notification)
                    ->withData([
                        'url' => route('records.show', $event->record->record_id),
                        'record_id' => (string)$event->record->record_id,
                        'user_id' => (string)$event->user->id,
                    ]);

                $messaging->sendMulticast($message, $adminTokens);

                Log::info("Admin notification sent via FCM for Record ID {$event->record->record_id} by user {$event->user->id}.");

            } catch (\Throwable $e) {
                Log::error("Failed to send admin notification via FCM for Record ID {$event->record->record_id}: " . $e->getMessage());
            }
        }
    }