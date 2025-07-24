<?php

namespace App\Observers;

use App\Models\MedicationReminder; // 通知を保存するモデル
use App\Models\RecordMedication;   // イベントを検知するモデル
use Illuminate\Support\Facades\Log; // デバッグ用にLogファサードを追加
use App\Models\Record; // ★追加: Record モデルを使用するため

class RecordMedicationObserver
{
    /**
     * Handle the RecordMedication "created" event.
     * RecordMedication が新しくデータベースに作成されたときに実行されます。
     */
    public function created(RecordMedication $recordMedication): void
    {
        Log::info('RecordMedication created event fired. is_completed: ' . ($recordMedication->is_completed ? 'true' : 'false'));

        // 新しく記録された内服薬が未完了の場合
        if (!$recordMedication->is_completed) {
            $this->createMedicationReminder($recordMedication);
            Log::info('MedicationReminder created for uncompleted medication.');
        }
    }

    /**
     * Handle the RecordMedication "updated" event.
     * RecordMedication がデータベースで更新されたときに実行されます。
     */
    public function updated(RecordMedication $recordMedication): void
    {
        // 今回の要件は「新しく作成された場合」に焦点を当てるため、updatedイベントは一旦シンプルに。
        // もし「完了から未完了に変わった場合」も通知したいなら、このロジックを調整します。
    }

    /**
     * MedicationReminder を作成するヘルパーメソッド
     */
    protected function createMedicationReminder(RecordMedication $recordMedication): void
    {
        try {
            // RecordMedication は Record に属しているはずなので、まず Record を取得
            $record = $recordMedication->record;

            if (!$record) {
                Log::error('Record not found for RecordMedication ID: ' . $recordMedication->record_medication_id);
                return; // Record がなければ通知は作成しない
            }

            // Record に関連付けられた User を取得
            $user = $record->user;

            // 患者名を取得（Record モデルに patient_name がない場合は User モデルの name を使う）
            $patientName = $record->patient_name ?? $user->name ?? '不明な患者';

            // ★★★ ここにdd()を追加 ★★★
            // MedicationReminder に渡されるデータを確認
            // dd([
            //     'user_id' => $user->id,
            //     'record_id' => $record->record_id,
            //     'patient_name' => $patientName,
            //     'event_type' => 'forgotten_medication',
            //     'message' => "{$patientName}さんの内服忘れが記録されました。",
            // ]);
            // ★★★ dd()追加ここまで ★★★

            MedicationReminder::create([
                'user_id' => $user->id,
                'record_id' => $record->record_id, // ★ここを追加
                'patient_name' => $patientName,
                'event_type' => 'forgotten_medication',
                'message' => "{$patientName}さんの内服忘れが記録されました。", // メッセージ本文もここで生成
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create MedicationReminder: ' . $e->getMessage());
            Log::error('RecordMedication ID: ' . $recordMedication->record_medication_id);
            Log::error('Error message: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
        }
    }
}