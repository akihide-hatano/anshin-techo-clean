<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Record;
use App\Models\User;
use App\Models\FcmToken;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;
use Kreait\Firebase\Factory;

class SendMedicationReminders extends Command
{
    /**
     * The name and signature of the console command.
     * コマンドの署名（名前と引数・オプション）を定義します。
     * @var string
     */
    protected $signature = 'medication:send-reminders';

    /**
     * The console command description.
     * コマンドの説明を定義します。
     * @var string
     */
    protected $description = 'ユーザーに内服リマインダーをFCMプッシュ通知で送信します。';

    protected $firebaseFactory;

    /**
     * Create a new command instance.
     * コマンドのコンストラクタで、Firebase Factoryを初期化します。
     * 依存性注入は行わず、env()から直接認証情報を取得します。
     */
    public function __construct() // Factory $firebaseFactory の引数は削除
    {
        parent::__construct();
        // Firebase Factoryを初期化する際に、明示的にサービスアカウントのパスを指定します。
        // .env ファイルから FIREBASE_CREDENTIALS の値を取得します。
        $this->firebaseFactory = (new Factory())->withServiceAccount(env('FIREBASE_CREDENTIALS'));
    }

    /**
     * Execute the console command.
     * コマンドが実行されたときに呼び出されるメソッドです。
     */
    public function handle()
    {
        Log::info('Medication reminder check started.');
        $this->info('Starting medication reminder process...');

        try {
            // Firebase Messagingインスタンスを作成します。
            $messaging = $this->firebaseFactory->createMessaging();
        } catch (\Exception $e) {
            $this->error('Failed to create Firebase Messaging instance: ' . $e->getMessage());
            Log::error('Firebase Messaging creation error: ' . $e->getMessage());
            return Command::FAILURE; // エラーが発生した場合はコマンドを失敗として終了
        }

        $now = Carbon::now();
        // 現在時刻から5分後までの範囲で、まだリマインダーが送信されていないレコードを取得します。
        $reminderWindowEnd = $now->copy()->addMinutes(5);

        $recordsToRemind = Record::where('reminder_sent', false)
                                ->whereBetween('taken_at', [$now, $reminderWindowEnd])
                                ->with(['user.fcmTokens', 'medications', 'timingTag']) // 関連するデータをEager Load
                                ->get();

        if ($recordsToRemind->isEmpty()) {
            Log::info('No medication reminders to send at this time.');
            $this->info('送信するリマインダーはありません。');
            return Command::SUCCESS; // 送信するリマインダーがない場合は成功として終了
        }

        $this->info(count($recordsToRemind) . '件のリマインダーをチェックしています...');

        foreach ($recordsToRemind as $record) {
            // ユーザーが存在し、FCMトークンが登録されている場合のみ通知を送信
            if ($record->user && $record->user->fcmTokens->isNotEmpty()) {
                $fcmTokens = $record->user->fcmTokens->pluck('token')->toArray();

                $timingName = $record->timingTag ? $record->timingTag->timing_name : '不明なタイミング';
                $notificationTitle = '内服リマインダー: ' . $timingName;
                $notificationBody = $record->taken_at->format('H時i分') . ' のお薬を服用する時間です！';

                $medicationNames = $record->medications->pluck('medication_name')->implode('、');
                if (!empty($medicationNames)) {
                    $notificationBody .= "\n服用する薬: {$medicationNames}";
                }

                try {
                    // Firebase通知オブジェクトを作成
                    $notification = FirebaseNotification::create($notificationTitle, $notificationBody);

                    // CloudMessageオブジェクトを作成し、通知とカスタムデータを設定
                    $message = CloudMessage::new()
                        ->withNotification($notification)
                        ->withData([
                            'url' => route('records.show', $record->record_id), // 通知クリック時に開くURL
                            'record_id' => (string)$record->record_id,
                        ]);

                    // 複数のFCMトークンにメッセージを送信
                    $messaging->sendMulticast($message, $fcmTokens);

                    // リマインダー送信後、reminder_sent フラグをtrueに更新
                    $record->update(['reminder_sent' => true]);

                    Log::info("FCM Reminder sent for Record ID {$record->record_id} to user {$record->user->id}.");
                    $this->info("FCMリマインダーを送信しました: Record ID {$record->record_id}");

                } catch (\Throwable $e) {
                    // 通知送信中にエラーが発生した場合のログとエラーメッセージ
                    Log::error("Failed to send FCM reminder for Record ID {$record->record_id}: " . $e->getMessage());
                    $this->error("FCMリマインダーの送信に失敗しました: Record ID {$record->record_id}. エラー: " . $e->getMessage());
                }
            } else {
                // FCMトークンがないユーザーへの警告ログ
                Log::warning("User {$record->user_id} has no FCM tokens for Record ID {$record->record_id}. Skipping reminder.");
                $this->warn("User {$record->user_id} のFCMトークンが見つかりません。Record ID {$record->record_id} のリマインダーをスキップしました。");
            }
        }

        Log::info('Medication reminder check finished.');
        $this->info('リマインダーチェックが完了しました。');

        return Command::SUCCESS; // コマンドを成功として終了
    }
}
