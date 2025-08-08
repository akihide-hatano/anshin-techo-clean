<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>内服忘れ通知</title>
</head>
<body>
    <h1>内服忘れのお知らせ</h1>
<p>{{ $user->name }} さんの内服記録が未完了です。</p>

<p>以下に詳細を記載します。</p>

<ul>
    <li>**薬の名前:** {{ $medication->medication_name }}</li>
    <li>**服用予定日:** {{ $record->taken_at->format('Y年m月d日 H:i') }}</li>
    <li>**未完了の理由:** {{ $reasonNotTaken ?? '（理由の記載なし）' }}</li>
</ul>

<p>確認をお願いします。</p>

<p>--</p>
<p>このメールはシステムからの自動送信です。</p>
</body>
</html>