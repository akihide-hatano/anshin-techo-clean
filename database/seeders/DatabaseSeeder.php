<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $this->call([
            TimingTagSeeder::class,    // 服用タイミングのシーダー
            MedicationSeeder::class,   // 薬情報のシーダー
            // RecordSeeder::class,       // 服用記録のシーダー (外部キーの関係上、他のシーダーの後に)
        ]);
    }
}
