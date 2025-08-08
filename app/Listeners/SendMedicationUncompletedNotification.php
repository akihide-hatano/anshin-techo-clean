<?php

namespace App\Listeners;

use App\Events\MedicationMarkedUncompleted;
use App\Mail\MedicationUncompletedMail; // Mailクラスのuseを追加
use App\Models\MedicationReminder; // モデルのuseを追加
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log; // Logファサードのuseを追加
use Illuminate\Support\Facades\Mail; // Mailファサードのuseを追加

class SendMedicationUncompletedNotification implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MedicationMarkedUncompleted $event): void
    {
        try {
            // イベントで渡されたデータを取得
            $record = $event->record;
            $medication = $event->medication;
            $reasonNotTaken = $event->reasonNotTaken;
            $user = $event->user;

            Log::info("Medication marked uncompleted event handled for User: {$user->id}, Record: {$record->record_id}, Medication: {$medication->medication_name}");

            // 内服忘れ通知を保存 (ダッシュボード用)
            MedicationReminder::create([
                'user_id' => $user->id,
                'record_id' => $record->record_id,
                'message' => '【内服忘れ】' . $medication->medication_name . 'の服用が未完了です。' . ($reasonNotTaken ? ' (' . $reasonNotTaken . ')' : ''),
                'is_read' => false,
            ]);

            // メール送信
            if ($user->notification_email) {
                Mail::to($user->notification_email)->send(new MedicationUncompletedMail($record, $medication, $reasonNotTaken));
                Log::info('Notification email sent successfully.');
            } else {
                Log::info('Notification email not set for user, skipping email send.');
            }
        } catch (\Exception $e) {
            Log::error('Failed to handle MedicationMarkedUncompleted event: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
        }
    }
}