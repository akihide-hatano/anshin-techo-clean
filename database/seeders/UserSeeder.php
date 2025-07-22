<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // Hashファサードをuse

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 一般ユーザーの例
        User::factory()->create([
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'user', // デフォルトは 'user'
        ]);

        // ★★★ 管理者ユーザーの例 ★★★
        User::factory()->create([
            'name' => '管理者ユーザー',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin', // ここを 'admin' に設定
        ]);

        // ★★★ 家族ユーザーの例を追加 ★★★
        User::factory()->create([
            'name' => '父',
            'email' => 'father@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        User::factory()->create([
            'name' => '母',
            'email' => 'mother@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        User::factory()->create([
            'name' => '妹',
            'email' => 'sister@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        // さらに多くのテストユーザーをファクトリで生成することもできます
        // User::factory(10)->create();
    }
}
