<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('服用タイミングの編集') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-6 text-center">服用タイミングを編集</h1>

                    {{-- バリデーションエラーの表示 --}}
                    @if ($errors->any())
                        <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-md">
                            <ul class="list-disc list-inside">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('timingtags.update', $timingtag) }}">
                        @csrf
                        @method('PUT') {{-- PUTメソッドを使用 --}}

                        {{-- タイミング名 --}}
                        <div class="mb-4">
                            <label for="timing_name" class="block text-sm font-medium text-gray-700">タイミング名 <span class="text-red-500">*</span></label>
                            <input type="text" name="timing_name" id="timing_name"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   value="{{ old('timing_name', $timingtag->timing_name) }}" required autofocus>
                            @error('timing_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- 基準時間 (HH:MM形式) --}}
                        <div class="mb-4">
                            <label for="base_time" class="block text-sm font-medium text-gray-700">基準時間 (例: 08:00)</label>
                            <input type="time" name="base_time" id="base_time"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   value="{{ old('base_time', $timingtag->base_time ? \Carbon\Carbon::parse($timingtag->base_time)->format('H:i') : '') }}">
                            @error('base_time')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- sort_order は使用しないとのことなので、このフィールドは含めません --}}

                        <div class="flex items-center justify-end mt-4">
                            {{-- 更新ボタン --}}
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                更新
                            </button>

                            {{-- キャンセル・詳細に戻るボタン --}}
                            <a href="{{ route('timingtags.show', $timingtag) }}" class="ml-4 inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                キャンセル
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>