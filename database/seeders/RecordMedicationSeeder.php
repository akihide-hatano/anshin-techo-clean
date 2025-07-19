<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Record;    // Recordモデルをインポート
use App\Models\Medication; // Medicationモデルをインポート
use App\Models\TimingTag; // TimingTagモデルはRecordSeederで使われるため、ここでは不要だが念のため残すことも可能

class RecordMedicationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 既存のデータをクリア（開発時のみ推奨）
        // record_medicationテーブルを直接truncate
        DB::table('record_medication')->truncate();

        // 必須データの取得
        $records = Record::all();      // 全ての服用イベント
        $medications = Medication::all(); // 全ての薬

        if ($records->isEmpty() || $medications->isEmpty()) {
            $this->command->info('必要な参照データ（服用記録、薬）が不足しています。');
            $this->command->info('RecordSeeder, MedicationSeederを先に実行してください。');
            return;
        }

        // 各服用記録に対して、ランダムな薬を紐付ける
        foreach ($records as $record) {
            // 各服用イベントでランダムな薬を1〜3種類選ぶ
            $numMedications = rand(1, 3);
            $selectedMedications = $medications->count() >= $numMedications ?
                                   $medications->random($numMedications) :
                                   $medications; // 薬が少ない場合は全て選択

            // random()が単一のモデルを返す場合があるので、コレクションに変換
            if (!($selectedMedications instanceof \Illuminate\Support\Collection)) {
                $selectedMedications = collect([$selectedMedications]);
            }

            foreach ($selectedMedications as $medication) {
                $isCompleted = (rand(0, 9) < 8); // 80%の確率で服用完了

                $randomDosage = rand(1, 10) . '錠'; // 1〜10のランダムな数字に「錠」を付与

                DB::table('record_medication')->insert([
                    'record_id' => $record->record_id, // RecordSeederで生成されたレコードID
                    'medication_id' => $medication->medication_id,
                    'taken_dosage' => $isCompleted ? $randomDosage : null, // 完了ならランダム用量、未完了ならnull
                    'is_completed' => $isCompleted, // 個々の薬の服用完了状態
                    'reason_not_taken' => $isCompleted ? null : (rand(0, 1) ? '飲み忘れ' : '体調不良'), // 未完了なら理由
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('RecordMedicationSeederが完了しました。');
    }
}