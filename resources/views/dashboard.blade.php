<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}

                    <!-- ★★★ ここに通知許可ボタンを追加 ★★★ -->
                    <div class="mt-4">
                        <button id="enable-notifications"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            プッシュ通知を有効にする
                        </button>
                    </div>
                    <!-- ★★★ ここまで ★★★ -->

                </div>
            </div>
        </div>
    </div>

    <!-- ★★★ ボタンにイベントリスナーを追加するスクリプト ★★★ -->
    <script>
        // ページロード後にボタンにイベントリスナーを追加します。
        // document.addEventListener('DOMContentLoaded', ...) は、HTMLの読み込みと解析が完了した後に
        // 指定された関数を実行するための標準的な方法です。
        document.addEventListener('DOMContentLoaded', function() {
            // ID 'enable-notifications' を持つボタン要素を取得します。
            const enableNotificationsButton = document.getElementById('enable-notifications');
            if (enableNotificationsButton) {
                // ボタンが存在する場合、クリックイベントリスナーを追加します。
                enableNotificationsButton.addEventListener('click', () => {
                    // ボタンがクリックされたら、app.blade.php で定義した
                    // window.requestNotificationPermission() 関数を呼び出します。
                    // この関数が、通知許可を求め、FCMトークンを取得してサーバーに送信する処理を行います。
                    window.requestNotificationPermission();
                });
            }
        });
    </script>
    <!-- ★★★ スクリプトここまで ★★★ -->
</x-app-layout>