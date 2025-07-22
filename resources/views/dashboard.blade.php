<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    {{ __("You're logged in!") }}

                    {{-- ★★★ ここから追加 ★★★ --}}
                    <div class="mt-4">
                        <button id="enable-notifications" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            プッシュ通知を有効にする
                        </button>
                        <p id="notification-status" class="mt-2 text-sm text-gray-600"></p>
                    </div>
                    {{-- ★★★ ここまで追加 ★★★ --}}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>