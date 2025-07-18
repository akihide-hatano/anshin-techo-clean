<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // DBファサードを使用

class TimingTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 既存のデータをクリア（開発時のみ推奨）
        DB::table('timing_tags')->truncate();

        DB::table('timing_tags')->insert([
            [
                'timing_name' => '朝食前', // 追加
                'base_time' => '07:30:00', // 例: 朝食の30分前
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'timing_name' => '朝食後',
                'base_time' => '08:00:00', // 例: 朝食の直後
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'timing_name' => '昼食前', // 追加
                'base_time' => '12:30:00', // 例: 昼食の30分前
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'timing_name' => '昼食後',
                'base_time' => '13:00:00', // 例: 昼食の直後
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'timing_name' => '夕食前', // 追加
                'base_time' => '18:30:00', // 例: 夕食の30分前
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'timing_name' => '夕食後',
                'base_time' => '19:00:00', // 例: 夕食の直後
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'timing_name' => '就寝前',
                'base_time' => '22:00:00',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'timing_name' => '頓服',
                'base_time' => null, // 頓服は特定の時間がないためnull
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'timing_name' => 'その他',
                'base_time' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}