<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold flex justify-center items-center gap-1">
                        <img src="{{ asset('images/pill.png') }}" alt="内服薬アイコン" class="size-12">
                        <span>登録されている薬一覧</span>
                    </h1>
                    <div class="sticky top-0 z-50 bg-white p-4 mb-6">
                        <form action="{{ route('medications.index') }}" method="GET" class="mb-0">
                            <div class="flex flex-wrap items-center">
                                <div class="w-full md:w-1/3 px-2 mb-4 md:mb-0">
                                    <label for="medication_name" class="block text-sm font-medium text-gray-90000">薬名</label>
                                    <input type="text" name="medication_name" id="medication_name" value="{{ $medicationName ?? '' }}" class="mt-1 block w-full rounded-md border-gray-400 shadow-sm">
                                </div>
                                <div class="w-full md:w-1/3 px-2 mb-4 md:mb-0">
                                    <label for="effect" class="block text-sm font-medium text-gray-90000">効果</label>
                                    <input type="text" name="effect" id="effect" value="{{ $effect ?? '' }}" class="mt-1 block w-full rounded-md border-gray-400 shadow-sm">
                                </div>
                                <div class="w-full md:w-1/3 px-2">
                                    <label for="side_effects" class="block text-sm font-medium text-gray-90000">副作用</label>
                                    <input type="text" name="side_effects" id="side_effects" value="{{ $sideEffects ?? '' }}" class="mt-1 block w-full rounded-md border-gray-400 shadow-sm">
                                </div>
                            </div>
                            <div class="mt-4 flex flex-col md:flex-row items-center gap-2">
                                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded w-full md:w-auto">
                                    検索
                                </button>
                                <a href="{{ route('medications.index') }}" class="mt-2 md:mt-0  bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded w-full md:w-auto text-center">
                                    リセット
                                </a>
                                <a href="{{ route('medications.create') }}" class="inline-flex items-center px-4 py-2 gap-1 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 focus:bg-gray-500 active:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                        <x-icons.document class="size-6 text-white" />
                                        新しい薬を追加
                                </a>
                            </div>
                        </form>
                    </div>
                    {{-- sticky対応のフォームここまで --}}

                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-600 bg-green-100 p-3 rounded-md">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($medications->isEmpty())
                        <p class="text-center text-gray-600 text-lg py-10">まだ薬が登録されていません。</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($medications as $medication)
                                <div class="bg-gray-200 border border-gray-200 rounded-lg shadow-md p-6 flex flex-col justify-between hover:shadow-lg hover:scale-105 transition-shadow duration-300 ease-in-out">
                                    {{-- 薬名を詳細ページへのリンクにする --}}
                                    <h2 class="text-xl font-semibold text-indigo-700 mb-2 border-b pb-2 border-gray-200">
                                        <a href="{{ route('medications.show', $medication) }}" class="hover:underline">
                                            {{ $medication->medication_name }}
                                        </a>
                                    </h2>
                                    <p class="text-gray-700 mb-1"><strong class="font-medium">用量:</strong> {{ $medication->dosage }}</p>
                                    <p class="text-gray-700 mb-1"><strong class="font-medium">効果:</strong> {{ $medication->effect }}</p>
                                    @if ($medication->side_effects)
                                        <p class="text-gray-700 mb-1"><strong class="font-medium">副作用:</strong> {{ $medication->side_effects }}</p>
                                    @endif
                                    @if ($medication->notes)
                                        <p class="text-gray-700"><strong class="font-medium">備考:</strong> {{ $medication->notes }}</p>
                                    @endif

                                    {{-- 詳細、編集、削除ボタン --}}
                                    <div class="mt-4 flex justify-end space-x-2">
                                        {{-- 詳細ボタン --}}
                                        <a href="{{ route('medications.show', $medication) }}" class="inline-flex items-center px-3 py-1.5 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500 focus:bg-blue-500 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            詳細
                                        </a>

                                        {{-- 編集ボタン --}}
                                        <a href="{{ route('medications.edit', $medication) }}" class="inline-flex items-center px-3 py-1.5 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-500 focus:bg-yellow-500 active:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            編集
                                        </a>

                                        {{-- 削除ボタン (フォームとして実装) --}}
                                        <form action="{{ route('medications.destroy', $medication) }}" method="POST" onsubmit="return confirm('本当にこの薬を削除しますか？');">
                                            @csrf
                                            @method('DELETE') {{-- DELETEメソッドを使用 --}}
                                            <button type="submit" class="inline-flex items-center px-3 py-1.5 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                                削除
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="mt-8">
                        {{ $medications->links() }}
                    </div>

                    <div class="mt-8 text-center">
                        <a href="{{ route('dashboard') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <x-icons.home class="size-6 text-white" />トップページに戻る
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>