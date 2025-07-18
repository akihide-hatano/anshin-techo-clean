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
// 服用タイミングのリソースルート
Route::resource('timing_tags', TimingTagController::class); // base_timeのCRUD用

require __DIR__.'/auth.php';
