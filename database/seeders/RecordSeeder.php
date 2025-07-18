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
        $daysToGenerate = 50; // 指定された日数に変更

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

                    foreach ($selectedMedications as $medication) {
                        $isCompleted = (rand(0, 9) < 8); // 80%の確率で服用完了 (以前の80%に戻しました。rand(0, 9) < 92 は常にtrueになるため)

                        $takenAt = null;
                        if ($timingTag->base_time) {
                            $takenAt = $currentDate->copy()->setTimeFromTimeString($timingTag->base_time);
                            // 実際には少しずれることをシミュレート
                            $takenAt->addMinutes(rand(-15, 15));
                        } else {
                            // base_timeがない場合はランダムな時間
                            $takenAt = $currentDate->copy()->addHours(rand(0, 23))->addMinutes(rand(0, 59));
                        }

                        // ★★★ ここから taken_dosage の修正 ★★★
                        $randomDosage = rand(1, 10) . '錠'; // 1〜10のランダムな数字に「錠」を付与
                        // ★★★ ここまで taken_dosage の修正 ★★★

                        $record = [
                            'user_id' => $user->id,
                            'medication_id' => $medication->medication_id,
                            'timing_tag_id' => $timingTag->timing_tag_id,
                            'is_completed' => $isCompleted,
                            'taken_dosage' => $isCompleted ? $randomDosage : null, // 完了ならランダム用量、未完了ならnull
                            'taken_at' => $isCompleted ? $takenAt : null, // 完了なら服用時間、未完了ならnull
                            'reason_not_taken' => $isCompleted ? null : (rand(0, 1) ? '飲み忘れ' : '体調不良'), // 未完了なら理由
                            'content' => $isCompleted ? '問題なく服用しました。' : '服用できませんでした。',
                            'created_at' => $currentDate->copy()->addHours(rand(0, 23))->addMinutes(rand(0, 59)),
                            'updated_at' => now(),
                        ];
                        DB::table('records')->insert($record);
                    }
                } else {
                    // 就寝前や頓服などもランダムで追加するかを検討
                    // ここではシンプルに、他の主要タイミングのみで一旦データを入れます
                    // 必要であれば、就寝前や頓服にもランダムに薬を割り当てて追加できます
                }
            }
        }

        $this->command->info('RecordSeederが完了しました。');
    }
}