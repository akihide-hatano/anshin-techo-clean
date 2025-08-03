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
            // ★デバッグログ開始
            Log::info('--- Starting handleUncompletedMedication process ---');
            Log::info('RecordMedication ID: ' . $recordMedication->id);

            // Record情報を取得
            $record = $recordMedication->record;
            if (!$record) {
                Log::error('Record not found for RecordMedication ID: ' . $recordMedication->id);
                return;
            }
            Log::info('Record ID found: ' . $record->record_id);

            // User情報を取得
            $user = $record->user;
            if (!$user) {
                Log::error('User not found for Record ID: ' . $record->record_id);
                return;
            }
            Log::info('User ID found: ' . $user->id);
            Log::info('User notification_email: ' . ($user->notification_email ?? 'null'));

            // 内服薬情報を取得
            $medicationName = $recordMedication->medication->medication_name ?? '不明な薬';
            Log::info('Medication Name: ' . $medicationName);

            // 内服忘れ通知を保存 (ダッシュボード用)
            MedicationReminder::create([
                'user_id' => $user->id,
                'record_id' => $record->record_id, // ★record_idを使用
                'message' => '【内服忘れ】' . $medicationName . 'の服用が未完了です。',
                'is_read' => false,
            ]);
            Log::info('MedicationReminder created successfully.');

            // メール送信
            if ($user->notification_email) {
                Log::info('Notification email is set. Attempting to send email to: ' . $user->notification_email);
                Mail::to($user->notification_email)->send(new MedicationUncompletedMail($record, $user));
                Log::info('Notification email sent successfully.');
            } else {
                Log::info('Notification email not set for user, skipping email send.');
            }

            Log::info('--- handleUncompletedMedication process finished ---');
        } catch (\Exception $e) {
            Log::error('Failed to handle uncompleted medication: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
        }
    }

    /**
     * Handle the RecordMedication "updated" event.
     */
    public function updated(RecordMedication $recordMedication): void
    {
    }
}