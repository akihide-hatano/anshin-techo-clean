<?php
return [
    // .envファイルで設定したFIREBASE_CREDENTIALS環境変数の値を取得し、
    // Firebase Admin SDKの認証情報として使用します。
    'credentials' => env('FIREBASE_CREDENTIALS'),
];