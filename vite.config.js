import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
        'resources/css/app.css',
        'resources/js/app.js',
        // Blade で @vite しているページ別エントリを全部のせる
        'resources/js/records-index.js',
        'resources/js/records-edit.js',
        'resources/js/records-create.js',
        'resources/js/calendar.js',
        'resources/css/calendar.css', // Blade で直接読み込んでいるため追加
            ],
            refresh: true,
        }),
    ],
    // ★★★ ここを再度追加/修正 ★★★
    server: {
        host: '0.0.0.0', // Dockerコンテナ内で全てのネットワークインターフェースからの接続を許可
        hmr: {
            host: 'localhost', // ブラウザがWebSocket接続を試みるホスト名
            clientPort: 5173,  // Viteのデフォルトポート (明示的に指定)
        },
        watch: {
            usePolling: true // ホットリロードが不安定な場合に試す
        }
    }
    // ★★★ ここまで再度追加/修正 ★★★
});