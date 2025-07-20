<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('内服記録の新規登録') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-6 text-center">新しい内服記録を登録</h1>

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

                    <form method="POST" action="{{ route('records.store') }}">
                        @csrf

                        {{-- 服用日時（日付のみ） --}}
                        <div class="mb-4">
                            <label for="taken_date" class="block text-sm font-medium text-gray-700">服用日 <span class="text-red-500">*</span></label>
                            <input type="date" name="taken_date" id="taken_date"
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                   value="{{ old('taken_date', \Carbon\Carbon::now()->format('Y-m-d')) }}" required>
                            {{-- 実際のタイムスタンプを送信するための隠しフィールド --}}
                            <input type="hidden" name="taken_at" id="taken_at_hidden" value="{{ old('taken_at', \Carbon\Carbon::now()->format('Y-m-d\TH:i:s')) }}">
                            @error('taken_date')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            @error('taken_at') {{-- hiddenフィールドのエラーも表示できるように --}}
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- 服用タイミング --}}
                        <div class="mb-4">
                            <label for="timing_tag_id" class="block text-sm font-medium text-gray-700">服用タイミング <span class="text-red-500">*</span></label>
                            <select name="timing_tag_id" id="timing_tag_id" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                                <option value="">選択してください</option>
                                @foreach($timingTags as $timingTag)
                                    <option value="{{ $timingTag->timing_tag_id }}"
                                            data-base-time="{{ $timingTag->base_time }}"
                                            {{ old('timing_tag_id') == $timingTag->timing_tag_id ? 'selected' : '' }}>
                                        {{ $timingTag->timing_name }} {{ $timingTag->base_time ? '(' . \Carbon\Carbon::parse($timingTag->base_time)->format('H:i') . ')' : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('timing_tag_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- 薬の選択と服用量入力 (複数選択可能) --}}
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">服用した薬 <span class="text-red-500">*</span></label>
                            <div id="medications-container">
                                @if(old('medications'))
                                    @foreach(old('medications') as $index => $oldMedication)
                                        <div class="flex items-center space-x-2 mb-2 medication-entry">
                                            <select name="medications[{{ $index }}][medication_id]" class="block w-2/3 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                                <option value="">薬を選択</option>
                                                @foreach($medications as $medication)
                                                    <option value="{{ $medication->medication_id }}"
                                                            {{ $oldMedication['medication_id'] == $medication->medication_id ? 'selected' : '' }}>
                                                        {{ $medication->medication_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="text" name="medications[{{ $index }}][taken_dosage]"
                                                   class="w-1/4 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                                   placeholder="服用量" value="{{ $oldMedication['taken_dosage'] ?? '' }}">
                                            <button type="button" class="text-red-600 hover:text-red-800 remove-medication-btn">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    @endforeach
                                @else
                                    {{-- デフォルトで1つの薬の入力欄を表示 --}}
                                    <div class="flex items-center space-x-2 mb-2 medication-entry">
                                        <select name="medications[0][medication_id]" class="block w-2/3 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" required>
                                            <option value="">薬を選択</option>
                                            @foreach($medications as $medication)
                                                <option value="{{ $medication->medication_id }}">
                                                    {{ $medication->medication_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <input type="text" name="medications[0][taken_dosage]"
                                               class="w-1/4 rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                                               placeholder="服用量">
                                        <button type="button" class="text-red-600 hover:text-red-800 remove-medication-btn">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd"></path>
                                            </svg>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <button type="button" id="add-medication-btn" class="mt-2 inline-flex items-center px-3 py-1.5 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-green-600 hover:bg-green-500 focus:outline-none focus:border-green-700 focus:ring active:bg-green-700 transition ease-in-out duration-150">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-11a1 1 0 10-2 0v2H7a1 1 0 100 2h2v2a1 1 0 102 0v-2h2a1 1 0 100-2h-2V7z" clip-rule="evenodd"></path>
                                </svg>
                                薬を追加
                            </button>
                            @error('medications.*.medication_id')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                            @error('medications.*.taken_dosage')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- 服用完了チェックボックス --}}
                        <div class="mb-4">
                            <input type="checkbox" name="is_completed" id="is_completed" value="1"
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                   {{ old('is_completed') ? 'checked' : '' }}>
                            <label for="is_completed" class="ml-2 text-sm text-gray-600">服用完了</label>
                        </div>

                        {{-- 服用しなかった理由 (is_completedがチェックされていない場合のみ表示) --}}
                        <div class="mb-6" id="reason_not_taken_field" style="{{ old('is_completed') ? 'display: none;' : '' }}">
                            <label for="reason_not_taken" class="block text-sm font-medium text-gray-700">服用しなかった理由</label>
                            <textarea name="reason_not_taken" id="reason_not_taken" rows="3"
                                      class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">{{ old('reason_not_taken') }}</textarea>
                            @error('reason_not_taken')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-end mt-4">
                            {{-- 登録ボタン --}}
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                登録
                            </button>

                            {{-- キャンセル・一覧に戻るボタン --}}
                            <a href="{{ route('records.index') }}" class="ml-4 inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-400 focus:bg-gray-400 active:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                キャンセル
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- JavaScriptファイルを読み込む --}}
    @vite('resources/js/records_create.js')

</x-app-layout>