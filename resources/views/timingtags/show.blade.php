<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('服用タイミングの詳細') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-6 text-center">服用タイミングの詳細情報</h1>

                    {{-- ステータスメッセージの表示 --}}
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

                    <div class="bg-gray-50 border border-gray-200 rounded-lg shadow-md p-6">
                        <h2 class="text-xl font-semibold text-indigo-700 mb-4 border-b pb-2 border-gray-200">
                            {{ $timingtag->timing_name }} {{-- ここは $timingtag (小文字) --}}
                        </h2>
                        <div class="space-y-3">
                            <p class="text-gray-700"><strong class="font-medium text-gray-800">基準時間:</strong> {{ $timingtag->base_time ?? '設定なし' }}</p>
                            {{-- sort_order は使用しないとのことなので、この行は含めません --}}
                            <p class="text-gray-600 text-sm mt-4">
                                <strong class="font-medium text-gray-700">登録日時:</strong> {{ $timingtag->created_at->format('Y/m/d H:i') }}
                            </p>
                            <p class="text-gray-600 text-sm">
                                <strong class="font-medium text-gray-700">最終更新日時:</strong> {{ $timingtag->updated_at->format('Y/m/d H:i') }}
                            </p>
                        </div>

                        {{-- アクションボタン --}}
                        <div class="mt-6 flex justify-start space-x-3">
                            {{-- 編集ボタン --}}
                            <a href="{{ route('timingtags.edit', $timingtag) }}" class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-500 focus:bg-yellow-500 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                編集
                            </a>

                            {{-- 削除ボタン (フォームとして実装) --}}
                            <form action="{{ route('timingtags.destroy', $timingtag) }}" method="POST" onsubmit="return confirm('本当にこの服用タイミングを削除しますか？\n関連する記録がある場合は削除できません。');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    削除
                                </button>
                            </form>

                            {{-- 一覧に戻るボタン --}}
                            <a href="{{ route('timingtags.index') }}" class="ml-4 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                一覧に戻る
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>