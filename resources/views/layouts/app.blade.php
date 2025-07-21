<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
    <script type="module">
        // npmでインストールしたFirebaseモジュールをインポート
        import { initializeApp } from 'firebase/app';
        import { getMessaging, getToken, onMessage } from 'firebase/messaging'; // Messaging関連の関数をインポート

        // ★★★ ここを新しいfirebaseConfigに置き換えてください ★★★
        const firebaseConfig = {
            apiKey: "AIzaSyBSsJCU6fRI6OLL2exCOiu1Oi30pPApOFQ",
            authDomain: "anshin-techo-87769.firebaseapp.com",
            projectId: "anshin-techo-87769",
            storageBucket: "anshin-techo-87769.firebasestorage.app",
            messagingSenderId: "174755315946",
            appId: "1:174755315946:web:7f8db8b02fd7f4f7ff9793"
        };
        // ★★★ 置き換えここまで ★★★

        // Firebaseアプリを初期化
        const app = initializeApp(firebaseConfig);

        // Messagingサービスを取得
        const messaging = getMessaging(app);

        // VAPID公開鍵 (Firebase ConsoleのCloud Messagingタブから取得したものをここに貼り付けます)
        // ★★★ あなたのVAPID公開鍵に置き換えてください ★★★
        const vapidKey = "BLIX_vYgDfl7y0m5Nu6o6Tb1DapKXSk9ZMZOAnbVwrTdH0HWfzD4PbfZGUCu3ElmsYElaMpG5N0yWyAZxiSTAAQ";

        // Service Workerの登録とFCMトークン取得・送信のロジック
        window.requestNotificationPermission = async () => {
            try {
                // Service Workerを登録します。
                // Viteを使用している場合、Service Workerファイルをpublicのルートに直接出力する設定が必要です。
                // vite.config.js の設定が正しければ、/firebase-messaging-sw.js でアクセスできます。
                const registration = await navigator.serviceWorker.register('/firebase-messaging-sw.js');
                // messaging.useServiceWorker(registration); // この行は新しいSDKでは不要になる場合がありますが、念のため残すか、動作確認後に削除検討

                const permission = await Notification.requestPermission();

                if (permission === 'granted') {
                    console.log('通知の許可が与えられました。');

                    const currentToken = await getToken(messaging, { vapidKey: vapidKey });
                    if (currentToken) {
                        console.log('FCMトークン:', currentToken);
                        await sendTokenToServer(currentToken);
                    } else {
                        console.warn('FCMトークンが利用できません。');
                    }
                } else {
                    console.warn('通知の許可が拒否されました。');
                }
            } catch (error) {
                console.error('通知許可またはトークン取得中にエラーが発生しました:', error);
            }
        };

        async function sendTokenToServer(token) {
            try {
                const response = await fetch('/api/fcm-token', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ token: token })
                });

                if (response.ok) {
                    console.log('FCMトークンがサーバーに正常に送信されました。');
                } else {
                    console.error('FCMトークンをサーバーに送信できませんでした:', response.statusText);
                }
            } catch (error) {
                console.error('FCMトークン送信中にエラーが発生しました:', error);
            }
        }

        // フォアグラウンドでのメッセージ受信ハンドラ
        onMessage(messaging, (payload) => {
            console.log('フォアグラウンドでメッセージを受信しました:', payload);
            const notificationTitle = payload.notification.title;
            const notificationOptions = {
                body: payload.notification.body,
                icon: payload.notification.icon || '/favicon.ico',
                data: payload.data // カスタムデータ。通知クリック時のURLなど、追加情報を格納できます。
            };
            new Notification(notificationTitle, notificationOptions);
        });
    </script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>
    </body>
</html>
