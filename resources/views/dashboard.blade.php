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
                            <a href="{{ route('medications.create') }}" class="w-full sm:w-auto px-6 py-3 rounded-md flex items-center gap-2  text-white font-semibold bg-gray-900 hover:bg-gray-800 transition-colors duration-200 text-center">
                                <x-icons.pills class="bg-white size-6" />
                                内服薬登録
                            </a>
                        <a href="{{ route('timingtags.create') }}" class="w-full sm:w-auto px-6 py-3 rounded-md flex items-center text-white font-semibold bg-sky-400 hover:bg-sky-500 transition-colors duration-200 text-center">
                            <x-icons.clock class="bg-white size-6" />
                            服薬タイミング登録
                        </a>
                    </div>

                    {{-- ここから新しいボタンを追加 --}}
                    <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 mb-8">
                        <a href="{{ route('medications.index') }}" class="w-full sm:w-auto px-6 py-3 rounded-md flex items-center gap-2 text-white font-semibold bg-gray-900 hover:bg-gray-800 transition-colors duration-200 text-center">
                            <x-icons.pills class="bg-white size-6" />
                            内服薬一覧
                        </a>
                        <a href="{{ route('timingtags.index') }}" class="w-full sm:w-auto px-6 py-3 rounded-md flex items-center gap-2 text-white font-semibold bg-sky-400 hover:bg-sky-500 transition-colors duration-200 text-center">
                            <x-icons.clock class="bg-white size-6" />
                            服薬タイミング一覧
                        </a>
                    </div>

<div class="mt-8">
    <h3 class="text-lg font-bold text-gray-700">本日の内服記録</h3>
    @if ($todayRecords->isNotEmpty())
        <div class="mt-2 space-y-4">
            @foreach ($todayRecords as $record)
                <div class="p-4 rounded-lg shadow-sm bg-white border border-gray-200">
                    <p class="text-gray-800 font-bold">
                        内服のタイミング：{{ $record->timingtag->timing_name }}
                    </p>
                    <ul class="mt-2 space-y-1 ml-4 list-disc list-inside text-gray-700">
                        @foreach ($record->medications as $medication)
                            <li class="flex items-center space-x-2">
                            <span>{{ $medication->medication_name }} - {{ $medication->pivot->taken_dosage }}錠</span>
                                @if ($medication->pivot->is_completed)
                                    <span class="text-green-600 font-semibold">（服用済み）</span>
                                @else
                                    <span class="text-red-600 font-semibold">（未服用）</span>
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    @else
        <p class="mt-2 text-gray-500">本日の内服記録はまだありません。</p>
    @endif
</div>

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