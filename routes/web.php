<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecordController;
use App\Http\Controllers\MedicationController;
use App\Http\Controllers\TimingTagController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

    // ★★★ 最も具体的なルートを、リソースルートよりも先に定義する ★★★

    // カレンダー表示ルート (records.calendar)
    Route::get('/records/calendar', [RecordController::class, 'calendar'])->name('records.calendar');

    // カレンダーイベントのAPIルート (records.getCalendarEvents)
    // これが /records/{record} よりも先にマッチするようにする
    Route::get('/records/events', [RecordController::class, 'getCalendarEvents'])->name('records.getCalendarEvents');

    // 内服記録の完了状態をトグルするAPIルート
    Route::post('/records/{record}/medications/{medication_id}/toggle-completion', [RecordController::class, 'toggleMedicationCompletionFromCalendar'])->name('records.toggleMedicationCompletionFromCalendar');

    // records リソースルート (records/{record} のような動的なセグメントを含む一般的なルート)
    // 上記の具体的なルートの後に配置する
    Route::resource('records', RecordController::class);
    // 薬のリソースルート
    Route::resource('medications', MedicationController::class);
    // 服用タイミングのリソースルート
    Route::resource('timingtags', TimingTagController::class);

require __DIR__.'/auth.php';
