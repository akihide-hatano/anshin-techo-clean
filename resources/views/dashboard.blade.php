<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-700 leading-tight">
            {{ __('ダッシュボード') }}
        </h2>
    </x-slot>

    <div class="py-12 bg-gray-100">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">

                {{-- フラッシュメッセージの表示 --}}
                @if (session('status'))
                    <div class="bg-sky-100 border border-sky-400 text-sky-700 px-4 py-3 rounded relative mb-4" role="alert">
                        <strong class="font-bold">成功！</strong>
                         <span class="block sm:inline">{{ session('status') }}</span>
                    </div>
                @endif
                {{-- フラッシュメッセージの表示ここまで --}}

                <div class="p-6 text-gray-700">
                    {{ __("ログイン中です！") }}

                    {{-- ここから新しいボタンを追加 --}}
                    <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 mb-8">
                        <a href="{{ route('medications.create') }}" class="w-full sm:w-auto px-6 py-3 rounded-md text-white font-semibold bg-gray-900 hover:bg-gray-800 transition-colors duration-200 text-center">
<svg  class="bg-white"    xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 640"><!--!Font Awesome Free v7.0.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M128 176C128 149.5 149.5 128 176 128C202.5 128 224 149.5 224 176L224 288L128 288L128 176zM240 432C240 383.3 258.1 338.8 288 305L288 176C288 114.1 237.9 64 176 64C114.1 64 64 114.1 64 176L64 464C64 525.9 114.1 576 176 576C213.3 576 246.3 557.8 266.7 529.7C249.7 501.1 240 467.7 240 432zM304.7 499.4C309.3 508.1 321 509.1 328 502.1L502.1 328C509.1 321 508.1 309.3 499.4 304.7C479.3 294 456.4 288 432 288C352.5 288 288 352.5 288 432C288 456.3 294 479.3 304.7 499.4zM361.9 536C354.9 543 355.9 554.7 364.6 559.3C384.7 570 407.6 576 432 576C511.5 576 576 511.5 576 432C576 407.7 570 384.7 559.3 364.6C554.7 355.9 543 354.9 536 361.9L361.9 536z"/></svg>
                            内服薬を登録
                        </a>
                        <a href="{{ route('timingtags.create') }}" class="w-full sm:w-auto px-6 py-3 rounded-md text-white font-semibold bg-sky-400 hover:bg-sky-500 transition-colors duration-200 text-center">
<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
</svg>

                            服薬タイミングを登録
                        </a>
                        {{-- records.show は、内服忘れ通知のリンクで対応するため、ここでは省略 --}}
                        {{-- medications.show, timings.show は、一覧ページを作成してそこに配置するのが一般的です --}}
                    </div>

                    {{-- ここから新しいボタンを追加 --}}
                    <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 mb-8">
                        <a href="{{ route('medications.index') }}" class="w-full sm:w-auto px-6 py-3 rounded-md text-white font-semibold bg-gray-900 hover:bg-gray-800 transition-colors duration-200 text-center">
                            内服薬の一覧
                        </a>
                        <a href="{{ route('timingtags.index') }}" class="w-full sm:w-auto px-6 py-3 rounded-md text-white font-semibold bg-sky-400 hover:bg-sky-500 transition-colors duration-200 text-center">
                            服薬タイミングの一覧
                        </a>
                    </div>
                    {{-- 新しいボタンの追加ここまで --}}

                    {{-- 新しいボタンの追加ここまで --}}

                    <div class="mt-8">
                        <h3 class="text-lg font-bold text-gray-700">最近の内服忘れ通知</h3>
                        @if ($medicationReminders->isEmpty())
                            <p class="mt-2 text-gray-500">最近の内服忘れの記録はありません。</p>
                        @else
                            <ul class="mt-2 space-y-4">
                                @foreach ($medicationReminders as $reminder)
                                    <li class="p-4 rounded-lg shadow-sm {{ $reminder->is_read ? 'bg-white' : 'bg-red-50 border border-red-200' }}">
                                        {{-- メッセージをリンクにする --}}
                                        {{-- record_id が存在する場合のみリンクを表示 --}}
                                        @if ($reminder->record_id)
                                            <a href="{{ route('records.show', $reminder->record_id) }}" class="text-gray-800 hover:text-sky-600 font-bold">
                                                {{ $reminder->message }}
                                            </a>
                                        @else
                                            <p class="text-gray-800 font-bold">{{ $reminder->message }}</p>
                                        @endif

                                        <p class="text-sm text-gray-500 flex items-center justify-between mt-1">
                                            <span>
                                                {{ $reminder->created_at->format('Y年m月d日 H時i分') }}に記録
                                                @if (!$reminder->is_read)
                                                    <span class="ml-2 px-3 py-1 text-xs font-semibold text-red-800 bg-red-200 rounded-full">未読</span>
                                                @endif
                                            </span>
                                            {{-- 既読にするボタン --}}
                                            @if (!$reminder->is_read)
                                                <form action="{{ route('medication-reminders.mark-as-read', $reminder->id) }}" method="POST">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="text-sky-600 hover:text-sky-800 text-sm font-medium ml-4">既読にする</button>
                                                </form>
                                            @endif
                                        </p>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>