    // public/firebase-messaging-sw.js

    // Firebase SDKのCDNバージョンをService Workerにインポートします。
    // Service Worker内では、import { ... } from 'firebase/app' のようなESモジュールのimport構文は使用できません。
    // 代わりに importScripts() を使って、CDN経由でSDKを読み込みます。
    // Firebase v9以降のSDKでは、'compat' バージョンを使用することで、Service Workerで利用可能なグローバルな 'firebase' オブジェクトを提供します。
    importScripts('https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js');
    importScripts('https://www.gstatic.com/firebasejs/9.23.0/firebase-messaging-compat.js');

    // Firebaseアプリを初期化します。
    // ここで設定する firebaseConfig は、ウェブページ（resources/js/app.js）で設定したものと全く同じである必要があります。
    // ★★★ あなたのFirebaseプロジェクトの値に置き換えてください ★★★
    const firebaseConfig = {
        apiKey: "AIzaSyBSsJCU6fRI6OLL2exCOiu1Oi30pPApOFQ",
        authDomain: "anshin-techo-87769.firebaseapp.com",
        projectId: "anshin-techo-87769",
        storageBucket: "anshin-techo-87769.firebasestorage.app",
        messagingSenderId: "174755315946",
        appId: "1:174755315946:web:7f8db8b02fd7f4f7ff9793"
    };
    // ★★★ 置き換えここまで ★★★

    // Firebaseアプリを初期化します。
    // firebase-app-compat.js を読み込むことで、グローバルな 'firebase' オブジェクトが利用可能になります。
    const app = firebase.initializeApp(firebaseConfig);

    // Messagingサービスを取得します。
    // firebase-messaging-compat.js を読み込むことで、グローバルな 'firebase.messaging()' が利用可能になります。
    const messaging = firebase.messaging(); // Service Worker内では引数なしで呼び出すことが多いです。

    // バックグラウンドでメッセージを受信したときのハンドラ
    // (self.addEventListener('push') を直接使用する方法。これは標準的なService Workerの通知処理です。)
    // ユーザーがウェブサイトを閉じていたり、ブラウザのタブがアクティブでない状態で
    // プッシュ通知が届いたときに、このハンドラがService Workerによって実行されます。
    self.addEventListener('push', (event) => {
        // payload を取得する前に、イベントデータが存在するか確認します。
        // これにより、event.data が null の場合にエラーになるのを防ぎます。
        const payload = event.data ? event.data.json() : {};
        console.log('[firebase-messaging-sw.js] Push received:', payload);

        // Firebase Cloud Messaging (FCM) からの通知データは 'notification' プロパティに含まれることが多いです。
        const notificationData = payload.notification || {};
        // カスタムデータ（あなたが送信したい追加情報）は 'data' プロパティに含まれることが多いです。
        const customData = payload.data || {};

        const notificationTitle = notificationData.title || '通知'; // タイトルがなければデフォルトを設定
        const notificationOptions = {
            body: notificationData.body || '新しいメッセージがあります。', // 本文がなければデフォルトを設定
            icon: notificationData.icon || '/favicon.ico', // 通知アイコンのパス (サイトのファビコンをデフォルトに)
            data: customData // 通知クリック時に使用するカスタムデータ
        };

        // ブラウザのAPI (self.registration.showNotification) を使って通知を表示します。
        // event.waitUntil() は、通知の表示が完了するまでService Workerをアクティブに保ちます。
        event.waitUntil(
            self.registration.showNotification(notificationTitle, notificationOptions)
        );
    });

    // 通知がクリックされたときのハンドラ
    // ユーザーがOSレベルで表示されたプッシュ通知をクリックしたときに、このイベントがService Workerによって実行されます。
    self.addEventListener('notificationclick', (event) => {
        event.notification.close(); // クリックされた通知を閉じます。

        // 通知にカスタムデータ（event.notification.data）としてURLが含まれていれば、そのURLを新しいタブで開きます。
        if (event.notification.data && event.notification.data.url) {
            // clients.openWindow() は、新しいブラウザウィンドウまたはタブを開きます。
            // event.waitUntil() は、Service Workerがこの処理を完了するまで待機するようにブラウザに指示します。
            event.waitUntil(clients.openWindow(event.notification.data.url));
        }
    });

    // 以下は、Firebase SDKの onBackgroundMessage を利用する場合のコード例です。
    // 上記の self.addEventListener('push') と同時に有効にすると、同じメッセージで二重に通知が表示される可能性があります。
    // 通常はどちらか一方を使用します。直接 Service Workerで通知を制御したい場合は self.addEventListener('push') を推奨します。
    // messaging.onBackgroundMessage((payload) => {
    //     console.log('[firebase-messaging-sw.js] Received background message from FCM SDK:', payload);
    //     const notificationTitle = payload.notification.title;
    //     const notificationOptions = {
    //         body: payload.notification.body,
    //         icon: payload.notification.icon,
    //         data: payload.data
    //     };
    //     self.registration.showNotification(notificationTitle, notificationOptions);
    // });
    