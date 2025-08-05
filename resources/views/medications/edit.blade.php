<x-app-layout>


    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold flex justify-center items-center gap-1 mb-3">
                        <img src="{{ asset('images/pill.png') }}" alt="内服薬アイコン" class="size-12">
                        <span>薬の詳細情報</span>
                    </h1>

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
                    <form method="POST" action="{{ route('medications.update', $medication) }}">
                        @csrf
                        @method('PUT')
                        {{-- 薬の名前 --}}
                        <div class="mb-4">
                            <label for="medication_name" class="block text-sm font-medium text-gray-700">薬の名前<span class="text-red-500">*</span></label>
                            <input type="text" name="medication_name" id="medication_name"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                value="{{ old('medication_name', $medication->medication_name) }}" required autofocus>
                            @error('medication_name')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- 用量 --}}
                        <div class="mb-4">
                            <label for="dosage" class="block text-sm font-medium text-gray-700">用量<span class="text-red-500">*</span></label>
                            <input type="text" name="dosage" id="dosage"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                value="{{ old('dosage', $medication->dosage) }}" required>
                            @error('dosage')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- 効果 --}}
                        <div class="mb-4">
                            <label for="effect" class="block text-sm font-medium text-gray-700">効果<span class="text-red-500">*</span></label>
                            <textarea name="effect" id="effect" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>{{ old('effect', $medication->effect) }}</textarea>
                            @error('effect')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- 副作用 --}}
                        <div class="mb-4">
                            <label for="side_effects" class="block text-sm font-medium text-gray-700">副作用<span class="text-red-500">*</span></label>
                            <textarea name="side_effects" id="side_effects" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('side_effects', $medication->side_effects) }}</textarea>
                            @error('side_effects')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- 備考 --}}
                        <div class="mb-6">
                            <label for="notes" class="block text-sm font-medium text-gray-700">備考</label>
                            <textarea name="notes" id="notes" rows="3"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('notes', $medication->notes) }}</textarea>
                            @error('notes')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            {{-- 更新ボタン --}}
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                薬を更新
                            </button>

                            {{-- キャンセル・詳細に戻るボタン --}}
                            <a href="{{ route('medications.show', $medication) }}" class="ml-4 inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                キャンセル
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>