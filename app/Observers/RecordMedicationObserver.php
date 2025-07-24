<?php

namespace App\Observers;

use App\Models\MedicationReminder; // 通知を保存するモデル
use App\Models\RecordMedication;   // イベントを検知するモデル
use Illuminate\Support\Facades\Log; // デバッグ用にLogファサードを追加

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
        Log::info('RecordMedication updated event fired. is_completed: ' . ($recordMedication->is_completed ? 'true' : 'false') . ', isDirty: ' . ($recordMedication->isDirty('is_completed') ? 'true' : 'false'));

        // 更新された内服薬が未完了になった、または未完了のまま更新された場合
        // かつ、is_completed の値が変更された場合 (isDirty('is_completed'))
        // または、元々未完了で、更新後も未完了のままの場合 (これはupdatedイベントのロジックで考慮)
        // 今回の要件は「新しく作成された場合」に焦点を当てるため、updatedイベントは一旦シンプルに。
        // もし「完了から未完了に変わった場合」も通知したいなら、このロジックを調整します。
        // 現状はcreatedイベントで十分なはずです。
    }

    /**
     * MedicationReminder を作成するヘルパーメソッド
     */
    protected function createMedicationReminder(RecordMedication $recordMedication): void
    {
        // 関連する Record のユーザーIDと患者名を取得
        // Record モデルに user() リレーションと patient_name カラムが定義されている前提です。
        try {
            $user = $recordMedication->record->user;
            // Record モデルに patient_name がない場合は、User モデルの name を使うなど調整が必要です。
            $patientName = $recordMedication->record->patient_name ?? $user->name ?? '不明な患者';

            MedicationReminder::create([
                'user_id' => $user->id,
                'patient_name' => $patientName,
                'event_type' => 'forgotten_medication',
                'message' => "{$patientName}さんの内服忘れが記録されました。", // メッセージ本文もここで生成
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to create MedicationReminder: ' . $e->getMessage());
            Log::error('RecordMedication ID: ' . $recordMedication->record_medication_id);
        }
    }
}