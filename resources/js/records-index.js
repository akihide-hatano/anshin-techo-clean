document.addEventListener('DOMContentLoaded', function() {
    // 全ての .medication-entry (薬の項目) 内の is_completed 表示要素を取得
    const completionSpans = document.querySelectorAll('[data-is-completed]');

    completionSpans.forEach(span => {
        const isCompleted = span.dataset.isCompleted === 'true'; // 文字列 'true'/'false' を真偽値に変換
        const reason = span.dataset.reason;

        if (isCompleted) {
            span.textContent = '✅ 完了';
            span.classList.add('text-green-600'); // Tailwind CSSで緑色に
            span.classList.remove('text-red-600', 'text-yellow-600');
        } else {
            span.textContent = '❌ 未完了';
            span.classList.add('text-red-600'); // Tailwind CSSで赤色に
            span.classList.remove('text-green-600', 'text-yellow-600');

            if (reason) {
                // 未完了で理由がある場合、ホバー時に理由を表示するツールチップや詳細表示を検討
                // 例として、括弧内に理由を追加
                span.textContent += ` (${reason})`;
                // span.classList.add('text-yellow-600'); // 未完了だが理由付きで強調する場合
            }
        }
    });
});