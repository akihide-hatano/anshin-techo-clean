<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FCMTokenController; // 追加

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// ★★★ ここから追加 ★★★
Route::get('/test', function () {
    return response()->json(['message' => 'Test API route works!']);
});
// ★★★ ここまで追加 ★★★

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// FCMトークンを保存するためのルート（ここを追加）
Route::middleware('auth:sanctum')->post('/fcm-token', [FCMTokenController::class, 'store']);