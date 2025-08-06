<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-6 text-center">内服カレンダー</h1>
                    {{-- FullCalendarが表示されるコンテナ --}}
                    <div id="calendar" class="w-full h-auto"></div>

                    <div class="flex justify-center mt-8">
                        <a href="{{ route('records.index') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('一覧に戻る') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- FullCalendarのCSSを読み込む --}}
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.min.css' rel='stylesheet' />

 {{-- ★★★ 変更点: `@vite`でCSSとJSをまとめて読み込む ★★★ --}}
    @vite(['resources/css/calendar.css', 'resources/js/calendar.js'])

</x-app-layout>