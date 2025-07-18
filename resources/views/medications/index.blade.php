<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('薬一覧') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-6 text-center">登録されている薬一覧</h1>

                    @if ($medications->isEmpty())
                        <p class="text-center text-gray-600 text-lg py-10">まだ薬が登録されていません。</p>
                    @else
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($medications as $medication)
                                <div class="bg-gray-50 border border-gray-200 rounded-lg shadow-md p-6 flex flex-col justify-between hover:shadow-lg transition-shadow duration-300 ease-in-out">
                                    <h2 class="text-xl font-semibold text-indigo-700 mb-2 border-b pb-2 border-gray-200">
                                        {{ $medication->medication_name }}
                                    </h2>
                                    <p class="text-gray-700 mb-1"><strong class="font-medium">用量:</strong> {{ $medication->dosage }}</p>
                                    <p class="text-gray-700 mb-1"><strong class="font-medium">効果:</strong> {{ $medication->effect }}</p>
                                    @if ($medication->side_effects)
                                        <p class="text-gray-700 mb-1"><strong class="font-medium">副作用:</strong> {{ $medication->side_effects }}</p>
                                    @endif
                                    @if ($medication->notes)
                                        <p class="text-gray-700"><strong class="font-medium">備考:</strong> {{ $medication->notes }}</p>
                                    @endif
                                    {{-- ここに編集・削除ボタンなどを追加することも可能 --}}
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