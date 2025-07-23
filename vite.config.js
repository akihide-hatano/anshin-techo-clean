import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js','resources/js/firebase-messaging-sw.js'],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                entryFileNames: (chunkInfo) => {
                    if (chunkInfo.name === 'firebase-messaging-sw') {
                        return 'firebase-messaging-sw.js';
                    }
                    return `assets/[name]-[hash].js`;
                },
                assetFileNames: `assets/[name]-[hash].[ext]`,
            },
        },
    },
    // ★★★ ここから追加/修正 ★★★
    // server: {
    //     host: '0.0.0.0', // Dockerコンテナ内で全てのネットワークインターフェースからの接続を許可
    //     hmr: {
    //         host: 'localhost', // ブラウザがWebSocket接続を試みるホスト名
    //         clientPort: 5173,  // Viteのデフォルトポート (明示的に指定)
    //     },
    //     watch: {
    //         usePolling: true // ホットリロードが不安定な場合に試す
    //     }
    // }
    // ★★★ ここまで追加/修正 ★★★
});