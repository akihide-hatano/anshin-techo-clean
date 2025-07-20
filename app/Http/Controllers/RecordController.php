<?php

namespace App\Http\Controllers;

use App\Models\Record;
use App\Models\Medication; // Medication モデルを追加
use App\Models\TimingTag;  // TimingTag モデルを追加
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class RecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // まず、ユーザーがログインしていることを確認するガード句
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // ★Intelephense向けに型ヒントを追加★
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $records = $user->records()
                    ->with(['medications'])
                    ->orderBy('taken_at','desc')
                    ->paginate(10);

        // 未完了の薬があるかどうかをチェックするフラグ
        $hasUncompletedMedications = false;

        foreach ($records as $record) {
            foreach ($record->medications as $medication) {
                // is_completed が false の薬が一つでもあればフラグを立てる
                if (!$medication->pivot->is_completed) {
                    $hasUncompletedMedications = true;
                    // 一つでも見つかれば、これ以上チェックする必要はない
                    break 2; // 2階層のループを抜ける
                }
            }
        }

        return view('records.index', compact('records'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
