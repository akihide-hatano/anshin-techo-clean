<x-app-layout>
    <div class="py-12">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
            <div class="mt-8 overflow-hidden">
                <div class="p-6 bg-white overflow-hidden shadow-xl sm:rounded-lg">
                    <h1 class="text-2xl md:text-3xl text-center font-bold mb-4 text-gray-800">Anshin-App</h1>
                    <p class="text-base md:text-lg text-gray-600 mb-6">
                        Anshin-Appは、大切な内服管理をサポートし、毎日の健康を安心して見守るためのアプリです。
                    </p>
                    <ul class="space-y-4 text-gray-700">
                        <li class="flex items-start">
                            <span class="text-green-500 mr-2 mt-1">💊</span>
                            <div>
                                <h3 class="font-bold text-xl md:text-2xl">内服記録の管理</h3>
                                <p class="text-sm md:text-base">自分が内服を飲めたかを記録し、一目で確認できます。</p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <span class="text-blue-500 mr-2 mt-1">📅</span>
                            <div>
                                <h3 class="font-bold text-xl md:text-2xl">カレンダーで振り返り</h3>
                                <p class="text-sm md:text-base">カレンダー機能で過去の内服状況を簡単に振り返ることができます。</p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <span class="text-red-500 mr-2 mt-1">🔔</span>
                            <div>
                                <h3 class="font-bold text-xl md:text-2xl">内服漏れの通知</h3>
                                <p class="text-sm md:text-base">ダッシュボードに内服漏れをした際のメッセージが表示されます。</p>
                            </div>
                        </li>
                        <li class="flex items-start">
                            <span class="text-yellow-500 mr-2 mt-1">🤝</span>
                            <div>
                                <h3 class="font-bold text-xl md:text-2xl">大切な人との連携</h3>
                                <p class="text-sm md:text-base">設定すれば、大切な人に内服忘れのメールが届き、一緒に健康をサポートできます。</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="flex justify-center mt-8">
                @guest
                    <div class="flex space-x-4">
                        <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-lg text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('ログイン') }}
                        </a>
                        <a href="{{ route('register') }}" class="inline-flex items-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-lg text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            {{ __('新規登録') }}
                        </a>
                    </div>
                @endguest

                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-lg text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('ダッシュボードへ') }}
                    </a>
                @endauth
            </div>
        </div>
    </div>
</x-app-layout>