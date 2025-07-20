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

//内服記録のリソース
Route::resource('records', RecordController::class);
// 薬のリソースルート
Route::resource('medications', MedicationController::class);
//カレンダー関連のルート
Route::get('/records/calender'.[RecordController::class,'calander'])->name('records.calendar');
Route::get('/api/records/events', [RecordController::class, 'getCalendarEvents'])->name('api.records.events');
// 服用タイミングのリソースルート
Route::resource('timingtags', TimingTagController::class);

require __DIR__.'/auth.php';
