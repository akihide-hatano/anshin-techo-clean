<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('服用タイミング一覧') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-6 text-center">登録されている服用タイミング一覧</h1>

                    {{-- 新しい服用タイミングを追加ボタン --}}
                    <div class="flex justify-end mb-6">
                        <a href="{{ route('timingtags.create') }}" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:bg-green-500 active:bg-green-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            新しい服用タイミングを追加
                        </a>
                    </div>

                    {{-- フラッシュメッセージの表示 --}}
                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-600 bg-green-100 p-3 rounded-md">
                            {{ session('status') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="mb-4 font-medium text-sm text-red-600 bg-red-100 p-3 rounded-md">
                            {{ session('error') }}
                        </div>
                    @endif

                    @if ($timingTags->isEmpty())
                        <p class="text-center text-gray-600 text-lg py-10">まだ服用タイミングが登録されていません。</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($timingTags as $timingTag)
                                <div class="bg-gray-50 border border-gray-200 rounded-lg shadow-md p-6 flex flex-col justify-between hover:shadow-lg transition-shadow duration-300 ease-in-out">
                                    {{-- タイミング名を詳細ページへのリンクにする --}}
                                    <h2 class="text-xl font-semibold text-indigo-700 mb-2 border-b pb-2 border-gray-200">
                                        <a href="{{ route('timingtags.show', $timingTag) }}" class="hover:underline">
                                            {{ $timingTag->timing_name }}
                                        </a>
                                    </h2>
                                    <p class="text-gray-700 mb-1"><strong class="font-medium">基準時間:</strong> {{ $timingTag->base_time ?? '設定なし' }}</p>

                                    {{-- 詳細、編集、削除ボタン --}}
                                    <div class="mt-4 flex justify-end space-x-2">
                                        {{-- 詳細ボタン --}}
                                        <a href="{{ route('timingtags.show', $timingTag) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            詳細
                                        </a>

                                        {{-- 編集ボタン --}}
                                        <a href="{{ route('timingtags.edit', $timingTag) }}" class="inline-flex items-center px-3 py-1.5 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-500 focus:bg-yellow-500 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            編集
                                        </a>

                                        {{-- 削除ボタン (フォームとして実装) --}}
                                        <form action="{{ route('timingtags.destroy', $timingTag) }}" method="POST" onsubmit="return confirm('本当にこの服用タイミングを削除しますか？');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                削除
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="mt-8 text-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            トップページに戻る
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>