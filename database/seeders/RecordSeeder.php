<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\TimingTag;
use Carbon\Carbon;

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
        $user = User::first();
        $timingTags = TimingTag::all();

        if (!$user || $timingTags->isEmpty()) {
            $this->command->info('必要な参照データ（ユーザー、タイミングタグ）が不足しています。');
            $this->command->info('UserSeeder, TimingTagSeederを先に実行してください。');
            return;
        }

        $daysToGenerate = 50;

        for ($i = 0; $i < $daysToGenerate; $i++) {
            $currentDate = Carbon::today()->subDays($i);

            foreach ($timingTags as $timingTag) {
                if (!in_array($timingTag->timing_name, ['就寝前', '頓服', 'その他'])) {
                    // taken_at と content のロジックを削除
                    // $takenAt = null; // 不要
                    // if ($timingTag->base_time) { // 不要
                    //     $takenAt = $currentDate->copy()->setTimeFromTimeString($timingTag->base_time); // 不要
                    //     $takenAt->addMinutes(rand(-15, 15)); // 不要
                    // } else { // 不要
                    //     $takenAt = $currentDate->copy()->addHours(rand(0, 23))->addMinutes(rand(0, 59)); // 不要
                    // }

                    // recordsテーブルに服用イベントの基本情報を挿入
                    $recordId = DB::table('records')->insertGetId([
                        'user_id' => $user->id,
                        'timing_tag_id' => $timingTag->timing_tag_id,
                        // 'taken_at' => $takenAt, // ★この行を削除またはコメントアウト★
                        // 'content' => 'この時間の薬を記録しました。', // ★この行を削除またはコメントアウト★
                        'created_at' => $currentDate->copy()->addHours(rand(0, 23))->addMinutes(rand(0, 59)),
                        'updated_at' => now(),
                    ], 'record_id');
                    
                    // ここから下の record_medication への挿入ロジックは
                    // RecordMedicationSeeder に移動したので削除済みであることを前提とします。
                    // もしまだ残っている場合は削除してください。
                }
            }
        }

        $this->command->info('RecordSeederが完了しました。');
    }
}