<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg px-5">
                {{-- フラッシュメッセージの表示 --}}
                @if (session('status'))
                    <div class="bg-sky-100 border border-red-600 text-red-800 mt-3 px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('status') }}</span>
                    </div>
                @endif
                {{-- 通知セクション --}}
                <div class="my-8">
                    <h3 class="text-lg font-bold text-gray-700 flex items-center gap-3 mb-3">
                        <x-icons.bell class="size-8 text-orange-500"/>
                        <span class="bg-[linear-gradient(transparent_95%,#c2410c_50%)] text-2xl">
                            最近の内服忘れ通知
                        </span>
                        </h3>
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
                {{-- 内服記録セクション --}}
                <div class="mt-8">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
                        <h3 class="text-2xl font-bold text-gray-700 flex items-center gap-2 mb-4 sm:mb-0">
                            <x-icons.check class="size-8 text-orange-700"/>
                            <span class="bg-[linear-gradient(transparent_95%,#c2410c_50%)]">本日の内服記録</span>
                        </h3>

                        <div class="flex flex-col sm:flex-row sm:space-x-2 space-y-2 sm:space-y-0 sm:w-auto">
                            <a href="{{ route('records.create') }}" class="px-4 py-2 rounded-md text-sm flex items-center justify-center gap-1 font-semibold text-white bg-gray-500 hover:bg-gray-600 transition-colors duration-200">
                                <x-icons.document class="size-6 text-white" />
                                記録を登録
                            </a>
                            <a href="{{ route('records.calendar') }}" class="px-4 py-2 rounded-md text-sm flex items-center justify-center gap-1 font-semibold text-white bg-indigo-400 hover:bg-indigo-600 transition-colors duration-200">
                                <x-icons.calendar class="size-6 text-white" />
                                カレンダー
                            </a>
                        </div>
                    </div>
                </div>

                    @if ($todayRecords->isNotEmpty())
                        <div class="mt-2 space-y-4">
                            @foreach ($todayRecords as $record)
                                <div class="p-4 rounded-lg shadow-sm bg-red-50 border border-red-300">
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

                <hr class="my-8 border-gray-700 border-3">

            {{-- 内服薬管理セクション --}}
            <div class="mt-8">
                <h3 class="text-2xl font-bold text-gray-700 mb-4 flex items-center">
                    <img src="{{ asset('images/pill.png') }}" alt="内服薬のアイコン" class="size-14 text-gray-700" />
                    <span class="bg-[linear-gradient(transparent_95%,#c2410c_50%)]">
                    内服薬の管理
                    </span>
                </h3>
                <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 mb-8">
                    <a href="{{ route('medications.create') }}" class="w-full sm:w-48 px-6 py-3 rounded-md flex items-center justify-center gap-2 text-white font-semibold bg-gray-500 hover:bg-gray-700 transition-colors duration-200 text-center">
                        <x-icons.document class="size-5 text-white" />
                        内服薬登録
                    </a>
                    <a href="{{ route('medications.index') }}" class="w-full sm:w-48 px-6 py-3 rounded-md flex items-center justify-center gap-2 text-white font-semibold bg-gray-500 hover:bg-gray-700 transition-colors duration-200 text-center">
                        <x-icons.search class="size-5 text-white" />
                        内服薬一覧
                    </a>
                </div>
            </div>

            <hr class="my-8 border-3 border-gray-300">

            {{-- 服薬タイミング管理セクション --}}
            <div class="mt-8">
                <h3 class="text-2xl font-bold text-gray-700 mb-4 flex items-center gap-2">
                    <x-icons.clock class="size-8 text-orange-700" />
                    <span class="bg-[linear-gradient(transparent_95%,#c2410c_50%)]">
                    服薬タイミングの管理
                    </span>
                </h3>
                <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 mb-8">
                    <a href="{{ route('timingtags.create') }}" class="w-full sm:w-48 px-6 py-3 rounded-md flex items-center justify-center gap-1 text-white font-semibold bg-amber-600 hover:bg-amber-700 transition-colors duration-200 text-center">
                        <x-icons.document class="size-5 text-white" />
                        服薬タイミング登録
                    </a>
                    <a href="{{ route('timingtags.index') }}" class="w-full sm:w-48 px-6 py-3 rounded-md flex items-center justify-center gap-1 text-white font-semibold bg-amber-600 hover:bg-amber-700 transition-colors duration-200 text-center">
                        <x-icons.search class="size-5 text-white" />
                        服薬タイミング一覧
                    </a>
                </div>
            </div>
                <hr class="my-8 border-gray-700 border-3">
            </div>
        </div>
    </div>
</x-app-layout>