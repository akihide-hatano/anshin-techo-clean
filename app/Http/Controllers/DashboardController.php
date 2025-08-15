<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MedicationReminder;
use App\Models\Record;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * ダッシュボードページを表示します。
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $userId = Auth::id(); // ログインしているユーザーのIDを取得

        // ログインユーザーに関連する未読の内服忘れ通知を取得
        $medicationReminders = MedicationReminder::where('user_id', $userId)
                                                ->where('is_read', false) // 未読のみ
                                                ->orderBy('created_at', 'desc')
                                                ->limit(5) // 例: 最新5件に制限
                                                ->get();

        //ログインユーザーの本日の内服薬記録を取得
        $todayRecords = Record::with(['medications','timingtag'])
                        ->where('user_id',$userId)
                        ->whereDate('created_at',today())
                        ->get();
        // ビューにデータを渡して表示
        return view('dashboard', compact('medicationReminders','todayRecords'));
    }
}