# 内服忘れ通知メール機能（開発者向け）

## 概要
このアプリには、服薬を記録していない利用者へメールでリマインドを送信する「内服忘れ通知機能」があります。
通知はキュー経由で非同期送信され、開発環境では **Mailpit** を使用して動作確認できます。

## 内服通知忘れフロー
<p align="center">
  <img src="public/images/mail_notification_flow.png" alt="内服忘れ通知フロー" width="800">
</p>


---

## 環境設定

`.env` ファイルで、メール送信関連の環境変数を設定してください。
Mailpitを使う場合の推奨設定は以下の通りです。

```env
MAIL_MAILER=smtp
MAIL_HOST=mailpit
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```

---

## 実装フロー（開発者向け — コード付き）
### 1. イベントの発火（Controller）
RecordController の store / update 内で、未完了判定時にイベントを発火します。

```php
// app/Http/Controllers/RecordController.php

use App\Events\MedicationMarkedUncompleted;
use Illuminate\Support\Facades\Auth;

// 省略...

if (! $isCompleted) {
    // 未完了の場合にイベントを発火
    event(new MedicationMarkedUncompleted($record, $medication, $reasonNotTaken, Auth::user()));
}
```

### 2. キューへの投入（EventServiceProvider）

`EventServiceProvider.php` で、イベントとリスナーの関連付けを設定します。
`MedicationMarkedUncompleted` イベントが発火すると、対応するリスナー `SendMedicationUncompletedNotification` がキューに登録されます。

```php
// app/Providers/EventServiceProvider.php

protected $listen = [
    MedicationMarkedUncompleted::class => [
        SendMedicationUncompletedNotification::class,
    ],
];
```

### 3. リスナーの実行（Queue Worker）

キューに登録されたジョブは、キューワーカーが `php artisan queue:work` コマンドで起動しているときに処理されます。  
リスナーの `handle` メソッド内で、`notification_email` が設定されていればメール送信処理が実行されます。

```php
// app/Listeners/SendMedicationUncompletedNotification.php

public function handle(MedicationMarkedUncompleted $event): void
{
    if ($event->user->notification_email) {
        Mail::to($event->user->notification_email)
            ->send(new MedicationUncompletedMail(
                $event->user,
                $event->record,
                $event->medication,
                $event->reasonNotTaken
            ));
    }
}
```

### 4. メールの構築と送信（Mailable クラス）

MedicationUncompletedMail クラスは、リスナーから渡されたデータを受け取り、メールの件名や本文（ビュー）を設定します。

```php
// app/Mail/MedicationUncompletedMail.php

public function content(): Content
{
    return new Content(
        view: 'emails.medication-uncompleted',
        with: [
            'user' => $this->user,
            'record' => $this->record,
            'medication' => $this->medication,
            'reasonNotTaken' => $this->reasonNotTaken,
        ],
    );
}

public function envelope(): Envelope
{
    return new Envelope(
        subject: '【安心手帳】内服忘れ通知',
    );
}

```
### 5. テスト方法
.env にMailpit設定を行い、sail up -d でコンテナを起動してください。
メール送信のキュー処理を動かすために、別ターミナルで以下を実行します。

```bash
sail artisan queue:work
```

フロントエンドのホットリロードも動かす場合は、別ターミナルで以下を実行します。

```bash
sail npm run dev
```

ブラウザで http://localhost にアクセスし、未完了の内服記録を登録すると、MailpitのWebUI（通常は http://localhost:8025）でメールが確認できます。



---

## 参考ドキュメント
- [Laravel Events - 公式ドキュメント](https://laravel.com/docs/12.x/events)
- [Laravel Mail - 公式ドキュメント](https://laravel.com/docs/12.x/mail)
- [Laravel Queues - 公式ドキュメント](https://laravel.com/docs/12.x/queues)
- [Mailpit - 公式GitHub](https://github.com/mailpit/mailpit)
