<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User; // Userモデルをインポート
use App\Notifications\PushNotification; // 作成した通知クラスをインポート

class SendTestPushNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'push:test {user_id} {title} {body} {--url=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test push notification to a specific user.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $userId = $this->argument('user_id');
        $title = $this->argument('title');
        $body = $this->argument('body');
        $url = $this->option('url');

        $user = User::find($userId);

        if (!$user) {
            $this->error('User with ID ' . $userId . ' not found.');
            return Command::FAILURE;
        }

        // ユーザーに通知を送信
        // UserモデルにNotifiableトレイトがuseされていることを確認
        $user->notify(new PushNotification($title, $body, $url));

        $this->info('Push notification sent to user ' . $user->name . ' (ID: ' . $user->id . ').');

        return Command::SUCCESS;
    }
}