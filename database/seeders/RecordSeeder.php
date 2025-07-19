<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Medication;
use App\Models\TimingTag;
use Carbon\Carbon; // 日付操作のためにCarbonをインポート

class RecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 既存のデータをクリア（開発時のみ推奨）
        // recordsテーブルのtruncateは、record_medicationテーブルもcascadeで削除するため、
        // record_medicationテーブルのtruncateは不要です。
        DB::table('records')->truncate();

        // 必須データの取得
        $user = User::first(); // 最初のユーザー (BreezeのTest Userを想定)
        $medications = Medication::all(); // 全ての薬
        $timingTags = TimingTag::all(); // 全ての服用タイミング

        if (!$user || $medications->isEmpty() || $timingTags->isEmpty()) {
            $this->command->info('必要な参照データ（ユーザー、薬、タイミングタグ）が不足しています。');
            $this->command->info('UserSeeder, MedicationSeeder, TimingTagSeederを先に実行してください。');
            return;
        }

        // データを生成する日数 (例: 過去50日間)
        $daysToGenerate = 50;

        // 各日付と各タイミングタグに対してレコードを作成
        for ($i = 0; $i < $daysToGenerate; $i++) {
            $currentDate = Carbon::today()->subDays($i);

            foreach ($timingTags as $timingTag) {
                // 就寝前、頓服、その他 以外の主要なタイミングには薬を割り当てる
                if (!in_array($timingTag->timing_name, ['就寝前', '頓服', 'その他'])) {
                    // 各タイミングでランダムな薬を1〜3種類選ぶ
                    $numMedications = rand(1, 3);
                    // 選択された薬が$medicationsの総数を超えないように調整
                    $selectedMedications = $medications->count() >= $numMedications ?
                                           $medications->random($numMedications) :
                                           $medications; // 薬が少ない場合は全て選択

                    // random()が単一のモデルを返す場合があるので、コレクションに変換
                    if (!($selectedMedications instanceof \Illuminate\Support\Collection)) {
                        $selectedMedications = collect([$selectedMedications]);
                    }

                    // 1. recordsテーブルに服用イベントの基本情報を挿入
                    // is_completed, taken_dosage, reason_not_taken は中間テーブルへ移動
                    // content はrecordsテーブルに残すか、record_medicationに移動するかで調整
                    // ここでは、recordsテーブルにcontentとtaken_atを残す前提で記述します
                    // もしrecordsテーブルからtaken_atとcontentを削除した場合は、以下の行も調整してください
                    $takenAt = null;
                    if ($timingTag->base_time) {
                        $takenAt = $currentDate->copy()->setTimeFromTimeString($timingTag->base_time);
                        $takenAt->addMinutes(rand(-15, 15)); // 実際には少しずれることをシミュレート
                    } else {
                        $takenAt = $currentDate->copy()->addHours(rand(0, 23))->addMinutes(rand(0, 59));
                    }

                    $recordId = DB::table('records')->insertGetId([
                        'user_id' => $user->id,
                        'timing_tag_id' => $timingTag->timing_tag_id,
                        'taken_at' => $takenAt, // 服用イベント全体の時刻
                        'content' => 'この時間の薬を記録しました。', // 服用イベント全体のメモ
                        'created_at' => $currentDate->copy()->addHours(rand(0, 23))->addMinutes(rand(0, 59)),
                        'updated_at' => now(),
                    ], 'record_id'); // PostgreSQLの場合、insertGetIdの第2引数で主キーのカラム名を指定

                    // 2. record_medicationテーブルに個々の薬の服用情報を挿入
                    foreach ($selectedMedications as $medication) {
                        $isCompleted = (rand(0, 9) < 8); // 80%の確率で服用完了

                        $randomDosage = rand(1, 10) . '錠'; // 1〜10のランダムな数字に「錠」を付与

                        DB::table('record_medication')->insert([
                            'record_id' => $recordId, // recordsテーブルで生成されたID
                            'medication_id' => $medication->medication_id,
                            'taken_dosage' => $isCompleted ? $randomDosage : null, // 完了ならランダム用量、未完了ならnull
                            'is_completed' => $isCompleted, // 個々の薬の服用完了状態
                            'reason_not_taken' => $isCompleted ? null : (rand(0, 1) ? '飲み忘れ' : '体調不良'), // 未完了なら理由
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }

        $this->command->info('RecordSeederが完了しました。');
    }
}