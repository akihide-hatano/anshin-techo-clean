<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Record;
use App\Models\TimingTag;
use Carbon\Carbon;

class RecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Record::truncate();

        $users = User::all();
        $timingTags = TimingTag::all();

        if ($users->isEmpty()) { // TimingTagがない場合はエラーではなく、ログ出力
            $this->command->info('必要な参照データ（ユーザー）が不足しています。UserSeederを先に実行してください。');
            return;
        }
        if ($timingTags->isEmpty()) {
            $this->command->info('必要な参照データ（服用タイミング）が不足しています。TimingTagSeederを先に実行してください。');
            // TimingTagがない場合でもレコードを作成できるようにするなら、nullableに設定するか、ここでreturn
            // 今回はtiming_tag_idがNOT NULLなので、ここでreturnする
            return;
        }

        foreach ($users as $user) {
            for ($i = 0; $i < 15; $i++) {
                $randomTimingTag = $timingTags->random();

                $randomDate = Carbon::now()->subDays(rand(0, 29))->startOfDay();

                $baseTime = $randomTimingTag->base_time;

                if ($baseTime) {
                    $parsedBaseTime = Carbon::parse($baseTime);
                    $takenAt = $randomDate->setTime(
                        $parsedBaseTime->hour,
                        $parsedBaseTime->minute,
                        $parsedBaseTime->second
                    );
                } else {
                    $takenAt = $randomDate->addHours(rand(0, 23))->addMinutes(rand(0, 59))->addSeconds(rand(0, 59));
                }

                // ★ここを修正！ taken_at と timing_tag_id を渡す★
                Record::create([
                    'user_id' => $user->id,
                    'timing_tag_id' => $randomTimingTag->timing_tag_id, // ここが重要！
                    'taken_at' => $takenAt,                             // ここが重要！
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        $this->command->info('RecordSeederが完了しました。');
    }
}