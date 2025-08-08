# 内服忘れ通知メール機能（開発者向け）

## 概要
このアプリには、服薬を記録していない利用者へメールでリマインドを送信する「内服忘れ通知機能」があります。
通知はキュー経由で非同期送信され、開発環境では **Mailpit** を使用して動作確認できます。

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

