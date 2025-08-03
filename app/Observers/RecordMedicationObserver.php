<?php

namespace App\Observers;

use App\Models\MedicationReminder;
use App\Models\RecordMedication;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\MedicationUncompletedMail;
use App\Models\Record;

class RecordMedicationObserver
{
    /**
     * Handle the RecordMedication "created" event.
     */
    public function created(RecordMedication $recordMedication): void
    {
        Log::info('RecordMedication created event fired. is_completed: ' . ($recordMedication->is_completed ? 'true' : 'false'));

        if (!$recordMedication->is_completed) {
            $this->handleUncompletedMedication($recordMedication);
        }
    }

    /**
     * 内服未完了時の処理
     * @param RecordMedication $recordMedication
     * @return void
     */
    protected function handleUncompletedMedication(RecordMedication $recordMedication): void
    {
        try {
            $record = $recordMedication->record;
            if (!$record) {
                Log::error('Record not found for RecordMedication ID: ' . $recordMedication->id);
                return;
            }

            $user = $record->user;
            if (!$user) {
                Log::error('User not found for Record ID: ' . $record->id);
                return;
            }

            // 内服忘れ通知を保存 (ダッシュボード用)
            MedicationReminder::create([
                'user_id' => $user->id,
                'record_id' => $record->id,
                'message' => '【内服忘れ】' . $recordMedication->medication->medication_name . 'の服用が未完了です。',
                'is_read' => false,
            ]);
            Log::info('MedicationReminder created for uncompleted medication.');

            // メール送信
            if ($user->notification_email) {
                Mail::to($user->notification_email)->send(new MedicationUncompletedMail($record, $user));
                Log::info('Notification email sent to: ' . $user->notification_email);
            }
        } catch (\Exception $e) {
            Log::error('Failed to handle uncompleted medication: ' . $e->getMessage());
        }
    }

    /**
     * Handle the RecordMedication "updated" event.
     */
    public function updated(RecordMedication $recordMedication): void
    {
    }
}