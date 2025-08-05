<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h1 class="text-2xl font-bold mb-6 text-center">新しい薬を登録する</h1>

                    {{-- バリデーションエラーメッセージの表示 --}}
                    @if ($errors->any())
                        <div class="mb-4 p-4 border border-red-400 bg-red-100 text-red-700 rounded-md">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('medications.store') }}" method="POST">
                        @csrf {{-- CSRF保護のため必須 --}}

                        <div class="mb-4">
                            <label for="medication_name" class="block text-gray-700 text-sm font-bold mb-2">薬名 <span class="text-red-500">*</span></label>
                            <input type="text" name="medication_name" id="medication_name" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('medication_name') }}" required>
                        </div>

                        <div class="mb-4">
                            <label for="dosage" class="block text-gray-700 text-sm font-bold mb-2">用量</label>
                            <input type="text" name="dosage" id="dosage" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" value="{{ old('dosage') }}">
                        </div>

                        <div class="mb-4">
                            <label for="effect" class="block text-gray-700 text-sm font-bold mb-2">効果</label>
                            <textarea name="effect" id="effect" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('effect') }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label for="side_effects" class="block text-gray-700 text-sm font-bold mb-2">副作用</label>
                            <textarea name="side_effects" id="side_effects" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('side_effects') }}</textarea>
                        </div>

                        <div class="mb-4">
                            <label for="notes" class="block text-gray-700 text-sm font-bold mb-2">備考</label>
                            <textarea name="notes" id="notes" rows="3" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">{{ old('notes') }}</textarea>
                        </div>

                        <div class="flex items-center justify-between mt-6">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition duration-300 transform hover:scale-110">
                                登録する
                            </button>
                            <a href="{{ route('medications.index') }}" class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800">
                                一覧に戻る
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>