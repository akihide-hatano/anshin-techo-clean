// resources/js/calendar.js

// FullCalendarのコアと必要なプラグインをインポート
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid'; // 月表示などのグリッド表示用プラグイン
import interactionPlugin from '@fullcalendar/interaction'; // 日付クリックなどのインタラクション用プラグイン

document.addEventListener('DOMContentLoaded', function() {
    // カレンダーを表示するHTML要素を取得
    var calendarEl = document.getElementById('calendar');

    // Calendarインスタンスを初期化
    var calendar = new Calendar(calendarEl, {
        // 使用するプラグイン
        plugins: [ dayGridPlugin, interactionPlugin ],

        // 初期表示ビュー
        initialView: 'dayGridMonth', // 月表示

        // ヘッダーツールバーの設定
        headerToolbar: {
            left: 'prev,next today', // 前月、次月、今日ボタン
            center: 'title', // カレンダーのタイトル（例: 2025年7月）
            right: 'dayGridMonth,dayGridWeek,dayGridDay' // 月、週、日表示ボタン
        },

        // 言語設定
        locale: 'ja', // 日本語に設定

        // イベントデータの取得元
        // Laravelのルート 'records.getCalendarEvents' からデータを取得します
        events: '/records/events', // このURLにGETリクエストを送信してイベントデータを取得

        // イベントがクリックされたときの処理（オプション）
        eventClick: function(info) {
            // イベントのURLがあれば、そのURLに遷移
            if (info.event.url) {
                // 新しいタブで開く
                window.open(info.event.url);
                info.jsEvent.preventDefault(); // デフォルトの動作（URL遷移）をキャンセル
            }
        },

        // イベントの色付けや表示に関するオプション（必要に応じて追加）
        eventDidMount: function(info) {
            // イベントの表示後にカスタム処理を行う場合
            // 例: 未完了のイベントに特定のスタイルを追加
            if (info.event.extendedProps.is_completed === false) {
                info.el.style.backgroundColor = '#FFC107'; // 未完了は黄色
                info.el.style.borderColor = '#FFC107';
            } else {
                info.el.style.backgroundColor = '#4CAF50'; // 完了は緑色
                info.el.style.borderColor = '#4CAF50';
            }
            // ツールチップなどを追加することも可能
            // info.el.title = info.event.extendedProps.description;
        },

        // 日付のクリックイベント（オプション）
        dateClick: function(info) {
            // 例: クリックした日付で新しい記録を作成するページに遷移
            // window.location.href = '/records/create?date=' + info.dateStr;
        },

        // カレンダーの高さ調整（レスポンシブ対応）
        height: 'auto', // コンテンツに合わせて自動調整
        contentHeight: 'auto', // コンテンツの高さも自動調整
        aspectRatio: 2.0, // アスペクト比 (幅/高さ) を設定してレスポンシブにする
    });

    // カレンダーを描画
    calendar.render();
});