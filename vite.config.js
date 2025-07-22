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
                // entryFileNames は、JavaScriptのエントリーポイントファイルの出力名を制御します。
                // firebase-messaging-sw.js の場合、ビルド時にそのままの名前で出力されるようにします。
                entryFileNames: (chunkInfo) => {
                    if (chunkInfo.name === 'firebase-messaging-sw') { // chunkInfo.name は 'firebase-messaging-sw' になります
                        return 'firebase-messaging-sw.js';
                    }
                    return `assets/[name]-[hash].js`; // その他のJSファイルは通常通り assets/ 以下に
                },
                // assetFileNames は、CSSや画像などのアセットファイルの出力名を制御します。
                // Service WorkerはJSファイルなので、通常は entryFileNames で制御されますが、
                // 念のため assetFileNames も含めておくとより確実です。
                assetFileNames: `assets/[name]-[hash].[ext]`,
            },
        },
    },
});
