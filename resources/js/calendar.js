// JavaScript (resources/js/calendar.js)
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';

document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    var calendar = new Calendar(calendarEl, {
        plugins: [ dayGridPlugin, interactionPlugin ],
        initialView: 'dayGridMonth',
        headerToolbar: {
            left: 'prev,next',
            center: 'title',
            right: 'dayGridMonth,dayGridWeek,dayGridDay'
        },
        locale: 'ja',
        events: '/records/events',

        buttonText: {
            today: '今日',
            month: '月',
            week: '週',
            day: '日',
            prev: '前', // または '先月'
            next: '次'  // または '翌月'
        },

        eventClick: function(info) {
            if (info.event.url) {
                window.open(info.event.url);
                info.jsEvent.preventDefault();
            }
        },

        eventDidMount: function(info) {
            // ★★★ 修正: ここを空にする ★★★
            // スタイルはCSSファイルに任せる
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