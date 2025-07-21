<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('内服記録詳細') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8"> {{-- max-w-3xl で少し横幅を狭める --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">

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

                    <h1 class="text-2xl font-bold mb-6 text-center text-indigo-700">内服記録詳細</h1>

                    <div class="bg-gray-100 border border-gray-300 rounded-lg p-6 mb-8 shadow-md">
                        <p class="text-lg text-gray-800 mb-3">
                            <strong class="font-semibold">{{ __('服用日時') }}:</strong>
                            <span class="ml-2">{{ $record->taken_at ? \Carbon\Carbon::parse($record->taken_at)->format('Y年m月d日 H時i分') : '-' }}</span>
                        </p>
                        <p class="text-lg text-gray-800 mb-3">
                            <strong class="font-semibold">{{ __('服用タイミング') }}:</strong>
                            <span class="ml-2">
                                @if ($record->timingTag)
                                    {{ $record->timingTag->timing_name }}
                                @else
                                    -
                                @endif
                            </span>
                        </p>

                        <div class="border-t border-gray-300 pt-4 mt-4">
                            <h3 class="text-xl font-semibold text-gray-800 mb-3">{{ __('服用した薬') }}</h3>
                            @if ($record->medications->isEmpty())
                                <p class="text-gray-600">{{ __('この記録に関連付けられた薬はありません。') }}</p>
                            @else
                                <ul class="list-disc pl-5 space-y-2">
                                    @foreach ($record->medications as $medication)
                                        <li class="text-gray-700">
                                            <strong class="font-medium">{{ $medication->medication_name }}</strong>
                                            （用量: {{ $medication->pivot->taken_dosage ?? '未入力' }}）
                                            @php
                                                $isCompleted = $medication->pivot->is_completed;
                                                $reasonNotTaken = $medication->pivot->reason_not_taken;
                                            @endphp
                                            <span class="ml-2 font-bold {{ $isCompleted ? 'text-green-600' : 'text-red-600' }}">
                                                @if($isCompleted)
                                                    {{ __('完了') }}
                                                @else
                                                    {{ __('未完了') }}
                                                    @if ($reasonNotTaken)
                                                        <span class="text-red-500 text-sm">（理由: {{ $reasonNotTaken }}）</span>
                                                    @endif
                                                @endif
                                            </span>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>

                    {{-- 操作ボタン --}}
                    <div class="flex justify-center space-x-4 mt-8">
                        <a href="{{ route('records.edit', $record->record_id) }}"
                        class="inline-flex items-center px-6 py-3 bg-yellow-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-yellow-500 focus:bg-yellow-500 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('編集') }}
                        </a>

                        <form action="{{ route('records.destroy', $record->record_id) }}" method="POST" class="inline-block">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    class="inline-flex items-center px-6 py-3 bg-red-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-red-500 focus:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                    onclick="return confirm('本当にこの記録を削除しますか？')">
                                {{ __('削除') }}
                            </button>
                        </form>

                        <a href="{{ route('records.index') }}"
                        class="inline-flex items-center px-6 py-3 bg-gray-800 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('一覧に戻る') }}
                        </a>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>