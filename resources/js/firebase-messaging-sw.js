// Firebase SDKをService Workerにインポートします。
// Firebase v9以降のSDKでは、Service Worker向けに最適化されたモジュールパスを使用します。
import { initializeApp } from 'firebase/app';
import { getMessaging, onBackgroundMessage } from 'firebase/messaging/sw'; // Service Worker用のgetMessaging

// Firebaseアプリを初期化します。
// ここで設定する firebaseConfig は、ウェブページ（app.blade.php）で設定したものと全く同じである必要があります。
// ★★★ あなたのFirebaseプロジェクトの値（anshin-techo-87769 プロジェクト）に置き換えています ★★★
const firebaseConfig = {
    apiKey: "AIzaSyBSsJCU6fRI6OLL2exCOiu1Oi30pPApOFQ",
    authDomain: "anshin-techo-87769.firebaseapp.com",
    projectId: "anshin-techo-87769",
    storageBucket: "anshin-techo-87769.firebasestorage.app",
    messagingSenderId: "174755315946",
    appId: "1:174755315946:web:7f8db8b02fd7f4f7ff9793"
};

// VAPID公開鍵 (Firebase ConsoleのCloud Messagingタブから取得したものをここに貼り付けます)
// app.blade.php と同じ値を設定してください。
const vapidKey = "BLIX_vYgDfl7y0m5Nu6o6Tb1DapKXSk9ZMZOAnbVwrTdH0HWfzD4PbfZGUCu3ElmsYElaMpG5N0yWyAZxiSTAAQ";

// Firebaseアプリを初期化
const app = initializeApp(firebaseConfig);

// Messagingサービスを取得します。
// Service Worker内では、'firebase/messaging/sw' から getMessaging をインポートして使用します。
const messaging = getMessaging(app);

// バックグラウンドでのメッセージ受信ハンドラ
// ユーザーがウェブサイトを閉じていたり、ブラウザのタブがアクティブでない状態で
// プッシュ通知が届いたときに、このハンドラがService Workerによって実行されます。
onBackgroundMessage(messaging, (payload) => {
    console.log('[firebase-messaging-sw.js] バックグラウンドでメッセージを受信しました:', payload);

    // 通知のタイトルとオプションを構築します。
    // payload.notification には、FCMから送られてきた通知の基本的な情報（タイトル、本文など）が含まれます。
    const notificationTitle = payload.notification.title;
    const notificationOptions = {
        body: payload.notification.body,
        icon: payload.notification.icon || '/favicon.ico', // 通知アイコン (ウェブサイトのファビコンをデフォルトに)
        data: payload.data // カスタムデータ。通知クリック時のURLなど、追加情報を格納できます。
    };

    // ブラウザのAPIを使って通知を表示します。
    // self.registration は、Service Workerの登録オブジェクトです。
    // showNotification() を呼び出すことで、ユーザーのOSレベルで通知が表示されます。
    self.registration.showNotification(notificationTitle, notificationOptions);
});

// 通知がクリックされたときのハンドラ
// ユーザーがOSレベルで表示されたプッシュ通知をクリックしたときに、このイベントがService Workerによって実行されます。
self.addEventListener('notificationclick', (event) => {
    event.notification.close(); // クリックされた通知を閉じます。

    // 通知にカスタムデータ（payload.data）としてURLが含まれていれば、そのURLを新しいタブで開きます。
    if (event.notification.data && event.notification.data.url) {
        // event.waitUntil() は、Service Workerが処理を完了するまで待機するようにブラウザに指示します。
        // clients.openWindow() は、新しいブラウザウィンドウまたはタブを開きます。
        event.waitUntil(clients.openWindow(event.notification.data.url));
    }
});
