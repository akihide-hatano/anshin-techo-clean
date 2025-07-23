<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\FCMToken; // FCMToken モデルが存在することを確認

class FCMTokenController extends Controller
{
    /**
     * 受信したFCMトークンをデータベースに保存します。
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $user = Auth::user();

        if ($user) {
            // 既に同じトークンが登録されている場合は更新、そうでなければ新規作成
            FCMToken::updateOrCreate(
                ['user_id' => $user->id], // user_id で既存のトークンを探す
                ['token' => $request->token] // 見つかった場合はトークンを更新、見つからなければ新規作成
            );

            return response()->json(['message' => 'FCMトークンが正常に保存されました。'], 200);
        }

        return response()->json(['message' => '認証されていません。'], 401);
    }
}