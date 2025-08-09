<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
<div class="mb-4 flex flex-col md:flex-row items-center md:justify-between gap-4">

    {{-- 左側に配置するダミー要素 --}}
    <div class="hidden md:block flex-shrink-0 w-24"></div>

    {{-- 中央のコンテンツ（タイトルとアイコン、未内服数） --}}
    <div class="flex flex-col items-center justify-center w-full md:w-auto">
        {{-- タイトルとアイコン --}}
        <div class="flex items-center">
            <h1 class="text-2xl font-bold text-center">内服記録一覧</h1>
            <img src="{{ asset('images/medication-records.png') }}" alt="内服記録アイコン" class="h-14 w-auto ml-2">
        </div>
        {{-- 未内服数 --}}
        @if ($uncompletedRecordCount > 0)
        <p class="font-semibold text-red-500">
            未内服数: {{ $uncompletedRecordCount }}件
        </p>
        @endif
    </div>

    {{-- 右端のボタン群（検索フォームと新規記録ボタン） --}}
    <div class="w-full md:w-auto flex flex-row justify-end items-end space-x-2 md:space-x-4">
        {{-- 検索フォームとボタン --}}
        <form action="{{ route('records.index') }}" method="GET" class="flex items-center space-x-2">
            {{-- 完了ステータスのドロップダウン --}}
            <div class="flex flex-col">
                <label for="completion" class="block text-sm font-medium text-gray-700">内服ステータス</label>
                <select name="completion" id="completion" class="mt-1 block pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                    <option value="">すべて</option>
                    <option value="uncompleted" {{ request('completion') === 'uncompleted' ? 'selected' : '' }}>未完了</option>
                    <option value="completed" {{ request('completion') === 'completed' ? 'selected' : '' }}>完了</option>
                </select>
            </div>
            
            {{-- 検索ボタン --}}
            <div class="self-end">
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    検索
                </button>
            </div>
        </form>

        {{-- 既存の新規記録を追加ボタン --}}
        <a href="{{ route('records.create') }}"
            class="inline-flex items-end px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
            {{ __('新規記録を追加') }}
        </a>
    </div>
</div>
                    @if ($records->isEmpty())
                        <div class="bg-gray-50 border border-gray-200 rounded-lg shadow-md p-6">
                            <p class="text-center text-gray-700">{{ __('まだ内服記録がありません。') }}</p>
                        </div>
                    @else
                    {{-- 成功メッセージの表示 --}}
                    @if (session('success'))
                        <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded-md">
                            {{ session('success') }}
                        </div>
                    @endif
                    {{-- エラーメッセージの表示 --}}
                    @if (session('error'))
                        <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-md">
                            {{ session('error') }}
                        </div>
                    @endif
                        {{-- カードグリッドコンテナ --}}
                        <div class="grid grid-cols-1 md:grid-cols-2  lg:grid-cols-3 gap-6"> {{-- 中画面以上で2列表示、ギャップは6 --}}
                            @foreach ($records as $record)
                                <div class="bg-white border border-gray-200 rounded-lg shadow-md overflow-hidden flex flex-col h-full"> {{-- カードの基本スタイルと高さを揃えるflex --}}
                                    {{-- カードヘッダー --}}
                                    <div class="px-6 py-4 bg-gray-100 border-b border-gray-200">
                                        <h3 class="text-lg font-semibold text-gray-800 mb-0">
                                            {{ __('服用日時') }}: {{ $record->taken_at ? \Carbon\Carbon::parse($record->taken_at)->format('Y/m/d H:i') : '-' }}
                                        </h3>
                                    </div>
                                    @if ($record->record_has_uncompleted)
                                        <div class="px-6 py-3 bg-red-100 border-b border-red-400 text-red-700 text-center text-sm font-bold -mx-6 -mt-2 mb-2">
                                            {{ __('この記録には未完了の薬があります。') }}
                                        </div>
                                    @endif
                                    {{-- カードボディ --}}
                                    <div class="p-6 flex-grow"> {{-- flex-growでコンテンツ領域を広げ、フッターを下に固定 --}}
                                        <p class="text-gray-700 mb-3">
                                            <strong class="font-medium text-gray-800">{{ __('服用タイミング') }}:</strong>
                                            @if ($record->timingTag)
                                                {{ $record->timingTag->timing_name }}
                                            @else
                                                -
                                            @endif
                                        </p>
                                        <p class="text-gray-700 mb-0">
                                            <strong class="font-medium text-gray-800">{{ __('薬の種類') }}:</strong>
                                            @if ($record->medications->isEmpty())
                                                <span class="block mt-1">{{ __('なし') }}</span>
                                            @else
                                                <ul class="list-none pl-0 mt-1 space-y-1"> {{-- リストスタイルを削除し、マージンとスペースを追加 --}}
                                                    @foreach ($record->medications as $medication)
                                                        <li class="text-sm">
                                                            {{ $medication->medication_name }} ({{ $medication->pivot->taken_dosage ?? '-' }})
                                                            {{-- is_completedの表示 --}}
                                                            @php
                                                                $isCompleted = $medication->pivot->is_completed;
                                                                $reasonNotTaken = $medication->pivot->reason_not_taken;
                                                            @endphp
                                                            <span class="ml-2 font-bold"
                                                                data-is-completed="{{ $isCompleted ? 'true' : 'false' }}"
                                                                data-reason="{{ $reasonNotTaken ?? '' }}">
                                                            </span>
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        </p>
                                    </div>
                                    {{-- カードフッター（操作ボタン） --}}
                                    <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end items-center space-x-2"> {{-- ボタン間のスペース --}}
                                        <a href="{{ route('records.show', $record->record_id) }}"
                                        class="inline-flex items-center px-3 py-1.5 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-400 focus:bg-blue-400 active:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            {{ __('詳細') }}
                                        </a>
                                        <a href="{{ route('records.edit', $record->record_id) }}"
                                        class="inline-flex items-center px-3 py-1.5 bg-yellow-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-400 focus:bg-yellow-400 active:bg-yellow-600 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            {{ __('編集') }}
                                        </a>
                                        <form action="{{ route('records.destroy', $record->record_id) }}" method="POST" class="inline-block">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex items-center px-3 py-1.5 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                                    onclick="return confirm('本当に削除しますか？')">
                                                {{ __('削除') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- ページネーションリンク --}}
                        <div class="flex justify-center mt-8">
                            {{ $records->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    {{-- ★JavaScriptファイルを読み込む★ --}}
    @vite('resources/js/records-index.js')
</x-app-layout>