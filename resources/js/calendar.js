// resources/js/calendar.js

import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';

document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new Calendar(calendarEl, {
        plugins: [ dayGridPlugin, interactionPlugin ],
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,dayGridWeek,dayGridDay'
        },
        locale: 'ja',
        events: '/records/events',

        eventClick: function(info) {
            if (info.event.url) {
                window.open(info.event.url);
                info.jsEvent.preventDefault();
            }
        },

        eventDidMount: function(info) {
            // イベントの色を、未完了の薬があるかどうかで設定
            if (info.event.extendedProps.has_uncompleted_meds === true) {
                info.el.style.backgroundColor = '#FFC107'; // 未完了があれば黄色
                info.el.style.borderColor = '#FFC107';
            } else {
                info.el.style.backgroundColor = '#4CAF50'; // 全て完了なら緑色
                info.el.style.borderColor = '#4CAF50';
            }

            // ★★★ ここから追加: ホバー時のスタイル変更 ★★★
            // ホバー時の色を定義
            const hoverColor = info.event.extendedProps.has_uncompleted_meds ? '#E6B000' : '#388E3C'; // 濃い黄色または濃い緑色
            const defaultColor = info.event.extendedProps.has_uncompleted_meds ? '#FFC107' : '#4CAF50';
            
            // マウスが乗ったときのイベント
            info.el.addEventListener('mouseenter', function() {
                info.el.style.backgroundColor = hoverColor;
                info.el.style.borderColor = hoverColor;
                info.el.style.cursor = 'pointer'; // カーソルをポインターにする
            });

            // マウスが離れたときのイベント
            info.el.addEventListener('mouseleave', function() {
                info.el.style.backgroundColor = defaultColor;
                info.el.style.borderColor = defaultColor;
            });
            // ★★★ ここまで追加 ★★★

            // ツールチップとして詳細情報を表示
            info.el.title = info.event.extendedProps.description;
        },

        dateClick: function(info) {
            const clickedDate = info.dateStr;
            window.location.href = `/records/create?date=${clickedDate}`;
        },

        height: 'auto',
        contentHeight: 'auto',
        aspectRatio: 2.0,
        eventDisplay: 'block',
    });

    calendar.render();
});