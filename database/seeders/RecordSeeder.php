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
                    // recordsテーブルに服用イベントの基本情報を挿入
                    $recordId = DB::table('records')->insertGetId([
                        'user_id' => $user->id,
                        'created_at' => $currentDate->copy()->addHours(rand(0, 23))->addMinutes(rand(0, 59)),
                        'updated_at' => now(),
                    ], 'record_id');
                }
            }
        }

        $this->command->info('RecordSeederが完了しました。');
    }
}