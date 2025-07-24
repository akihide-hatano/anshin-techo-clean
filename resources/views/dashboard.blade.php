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

                    {{-- ★★★ ここから通知表示のコードを追加 ★★★ --}}
                    <div class="mt-8">
                        <h3 class="text-lg font-medium text-gray-900">最近の内服忘れ通知</h3>
                        @if ($medicationReminders->isEmpty())
                            <p class="mt-2 text-gray-600">最近の内服忘れの記録はありません。</p>
                        @else
                            <ul class="mt-2 space-y-2">
                                @foreach ($medicationReminders as $reminder)
                                    <li class="p-3 bg-red-50 border border-red-200 rounded-md">
                                        <p class="text-red-800">{{ $reminder->message }}</p>
                                        <p class="text-sm text-gray-500">
                                            {{ $reminder->created_at->format('Y年m月d日 H時i分') }}に記録
                                            @if (!$reminder->is_read)
                                                <span class="ml-2 px-2 py-0.5 text-xs font-semibold text-red-700 bg-red-200 rounded-full">未読</span>
                                            @endif
                                        </p>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>