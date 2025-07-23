import './bootstrap';
import Alpine from 'alpinejs';
import { initializeApp } from 'firebase/app';
import { getMessaging, getToken, onMessage } from 'firebase/messaging';

window.Alpine = Alpine;
Alpine.start();

// ★★★ ここから追加 ★★★

// Firebase設定はBladeテンプレートからグローバル変数として渡される想定
// resources/views/layouts/app.blade.php などで以下のように設定されているはず
// <script>
//     window.firebaseConfig = {
//         apiKey: "{{ config('services.firebase.api_key') }}",
//         authDomain: "{{ config('services.firebase.auth_domain') }}",
//         projectId: "{{ config('services.firebase.project_id') }}",
//         storageBucket: "{{ config('services.firebase.storage_bucket') }}",
//         messagingSenderId: "{{ config('services.firebase.messaging_sender_id') }}",
//         appId: "{{ config('services.firebase.app_id') }}",
//         measurementId: "{{ config('services.firebase.measurement_id') }}"
//     };
// </script>

// Firebaseアプリを初期化
const firebaseApp = initializeApp(window.firebaseConfig);
const messaging = getMessaging(firebaseApp);

// Service Workerの登録パス
const serviceWorkerPath = '/firebase-messaging-sw.js';

// FCMトークンをバックエンドに送信する関数
async function sendTokenToServer(token) {
    try {
        const response = await fetch('/api/fcm-token', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ token: token })
        });

        if (response.ok) {
            console.log('FCMトークンがサーバーに正常に送信されました。');
            document.getElementById('notification-status').textContent = 'プッシュ通知が有効になりました！';
        } else {
            const errorData = await response.json();
            console.error('FCMトークンのサーバー送信に失敗しました:', errorData);
            document.getElementById('notification-status').textContent = 'プッシュ通知の有効化に失敗しました。';
        }
    } catch (error) {
        console.error('FCMトークンのサーバー送信中にエラーが発生しました:', error);
        document.getElementById('notification-status').textContent = 'エラーが発生しました。';
    }
}

// プッシュ通知を有効にする処理
document.addEventListener('DOMContentLoaded', () => {
    const enableNotificationsButton = document.getElementById('enable-notifications');
    const notificationStatus = document.getElementById('notification-status');

    if (enableNotificationsButton) {
        enableNotificationsButton.addEventListener('click', async () => {
            notificationStatus.textContent = '通知を有効化中...';
            try {
                // 通知許可を要求
                const permission = await Notification.requestPermission();

                if (permission === 'granted') {
                    console.log('通知の許可が与えられました。');

                    // Service Workerを登録
                    const registration = await navigator.serviceWorker.register(serviceWorkerPath);
                    console.log('Service Workerが登録されました:', registration);

                    // FCMトークンを取得
                    const currentToken = await getToken(messaging, { serviceWorkerRegistration: registration });

                    if (currentToken) {
                        console.log('FCMトークン:', currentToken);
                        await sendTokenToServer(currentToken);
                    } else {
                        console.warn('FCMトークンを取得できませんでした。');
                        notificationStatus.textContent = 'FCMトークンを取得できませんでした。';
                    }
                } else {
                    console.warn('通知が拒否されました。');
                    notificationStatus.textContent = '通知が拒否されました。';
                }
            } catch (error) {
                console.error('プッシュ通知の有効化中にエラーが発生しました:', error);
                notificationStatus.textContent = 'エラーが発生しました。詳細はコンソールを確認してください。';
            }
        });
    }
});

// フォアグラウンドでのメッセージ受信処理 (任意)
onMessage(messaging, (payload) => {
    console.log('フォアグラウンドでメッセージを受信しました:', payload);
    // ここで受信したメッセージをUIに表示するなどの処理を追加できます
    alert('新しい通知: ' + payload.notification.title + ' - ' + payload.notification.body);
});

// ★★★ ここまで追加 ★★★