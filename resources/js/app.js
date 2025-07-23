
import { initializeApp } from 'firebase/app';
import { getMessaging, getToken, onMessage } from 'firebase/messaging'; // Messagingサービスをインポート

// ★ここにFirebase ConsoleからコピーしたfirebaseConfigオブジェクトを貼り付ける★
const firebaseConfig = {
    apiKey: "AIzaSyBSsJCU6fRI6OLL2exCOiu1Oi30pPApOFQ",
    authDomain: "anshin-techo-87769.firebaseapp.com",
    projectId: "anshin-techo-87769",
    storageBucket: "anshin-techo-87769.firebasestorage.app",
    messagingSenderId: "174755315946",
    appId: "1:174755315946:web:7f8db8b02fd7f4f7ff9793"
};

// Firebaseアプリを初期化
const app = initializeApp(firebaseConfig);

// Messagingサービスを取得
const messaging = getMessaging(app);

// Service Workerの登録パス
const serviceWorkerRegistrationPath = '/firebase-messaging-sw.js'; // 後で作成するService Workerのパス

// FCMトークンをリクエストし、サーバーに送信する関数
async function requestPermissionAndSaveToken() {
    if (!('Notification' in window)) {
        console.warn('このブラウザは通知に対応していません。');
        return;
    }

    if (Notification.permission === 'granted') {
        console.log('通知は既に許可されています。');
        getAndSaveToken();
        return;
    }

    if (Notification.permission === 'denied') {
        console.warn('通知は拒否されています。設定から変更してください。');
        return;
    }

    try {
        const permission = await Notification.requestPermission();
        if (permission === 'granted') {
            console.log('通知が許可されました。');
            getAndSaveToken();
        } else {
            console.warn('通知が拒否されました。');
        }
    } catch (error) {
        console.error('通知許可のリクエスト中にエラーが発生しました:', error);
    }
}

// FCMトークンを取得し、Laravelバックエンドに送信する関数
async function getAndSaveToken() {
    try {
        const currentToken = await getToken(messaging, { vapidKey: 'BLIX_vYgDfl7y0m5Nu6o6Tb1DapKXSk9ZMZOAnbVwrTdH0HWfzD4PbfZGUCu3ElmsYElaMpG5N0yWyAZxiSTAAQ', serviceWorkerRegistration: await navigator.serviceWorker.register(serviceWorkerRegistrationPath) });

        if (currentToken) {
            console.log('FCMトークン:', currentToken);
            // Laravelバックエンドにトークンを送信
            await sendTokenToServer(currentToken);
        } else {
            console.warn('FCMトークンを取得できませんでした。');
        }
    } catch (error) {
        console.error('FCMトークンの取得または保存中にエラーが発生しました:', error);
    }
}

// FCMトークンをLaravelバックエンドに送信する関数
async function sendTokenToServer(token) {
    try {
        const response = await fetch('/api/fcm-token', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // CSRFトークンをヘッダーに含める
            },
            body: JSON.stringify({ token: token })
        });

        if (response.ok) {
            console.log('FCMトークンがサーバーに正常に保存されました。');
        } else {
            const errorData = await response.json();
            console.error('FCMトークンのサーバー保存に失敗しました:', errorData);
        }
    } catch (error) {
        console.error('FCMトークン送信中にネットワークエラーが発生しました:', error);
    }
}

// アプリケーションがロードされたときに実行
document.addEventListener('DOMContentLoaded', () => {
    // 例: ボタンクリックで通知許可をリクエスト
    const enableNotificationsButton = document.getElementById('enable-notifications');
    if (enableNotificationsButton) {
        enableNotificationsButton.addEventListener('click', requestPermissionAndSaveToken);
    }

    // フォアグラウンドでのメッセージ受信
    onMessage(messaging, (payload) => {
        console.log('フォアグラウンドでメッセージを受信しました:', payload);
        // ここで独自の通知表示ロジックを実装できます
        // 例: 新しい通知を表示
        const notificationTitle = payload.notification.title;
        const notificationOptions = {
            body: payload.notification.body,
            icon: '/path/to/your/icon.png' // 通知アイコンのパス
        };
        new Notification(notificationTitle, notificationOptions);
    });
});