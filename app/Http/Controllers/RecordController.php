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

        // 各レコードに「未完了の薬があるか」を示すカスタム属性を追加
        // paginator からコレクションを取得し、each() で各モデルを処理
        $records->getCollection()->each(function ($record) {
            $record->record_has_uncompleted = $record->medications->contains(function ($medication) {
                return !$medication->pivot->is_completed;
            });
        });

        return view('records.index', compact('records'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // 認証ユーザーの薬ではなく、全ての薬を取得するように変更
        // Medication モデルがユーザーに紐づかないため、全件取得します。
        $medications = Medication::orderBy('medication_name')->get(); // ★ここを修正★

        // 服用タイミングはこれまで通り取得
        $timingTags = TimingTag::orderBy('timing_tag_id', 'asc')->get();

        // dd($medications,$timingTags);
        return view('records.create', compact('medications', 'timingTags'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //バリデーションルール
        $validated = $request->validate([
            'taken_date' => 'required|date', // 服用日（日付ピッカーからの入力）
            'timing_tag_id' => 'required|exists:timing_tags,timing_tag_id', // 服用タイミングID
            'medications' => 'required|array|min:1', // 少なくとも1つの薬が必須
            'medications.*.medication_id' => 'required|exists:medications,medication_id', // 各薬のID
            'medications.*.taken_dosage' => 'nullable|string|max:255', // 各薬の服用量
            'medications.*.is_completed' => 'nullable|boolean', // 各薬の服用完了チェックボックス (値は '1' または null)
            'medications.*.reason_not_taken' => 'nullable|string|max:255', // 各薬の服用しなかった理由 (ドロップダウンからの値)
        ]);

        // 服用タイミングのbase_timeを取得
        // TimingTag モデルから、選択された timing_tag_id に基づいて base_time を取得します。
        $timingTag = TimingTag::find($validated['timing_tag_id']);
        // base_time が存在しない場合のデフォルト値として '00:00:00' を設定します。
        $baseTime = $timingTag ? $timingTag->base_time : '00:00:00';

         // Carbon::parse() を使用して、日付文字列と時刻文字列を結合し、DateTimeオブジェクトに変換します。
        $takenAt = Carbon::parse($validated['taken_date'] . ' ' . $baseTime);

        //承認済みユーザーに紐付けてRecordを作成します。
        // ★Intelephense向けに型ヒントを追加★
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $record = $user->records()->create([
            'taken_at' => $takenAt,
            'timing_tag_id' => $validated['timing_tag_id'],
        ]);

         // record_medication 中間テーブルにデータをアタッチ（多対多のリレーションシップ）
        if (isset($validated['medications'])) {
            $pivotData = [];
            // 各薬のデータをループ処理
            foreach ($validated['medications'] as $medicationData) {
                // is_completed チェックボックスの値を取得
                // チェックボックスはチェックされていれば '1' を送信し、されていなければ何も送信しません。
                // そのため、isset() と値が '1' であるかを確認して boolean に変換します。
                $isCompleted = isset($medicationData['is_completed']) && $medicationData['is_completed'] === '1';

                // is_completed が false (未完了) の場合のみ reason_not_taken を保存
                // 完了している場合は理由をnullにします。
                $reasonNotTaken = null;
                if (!$isCompleted) {
                    $reasonNotTaken = $medicationData['reason_not_taken'] ?? null;
                }

                // pivotData 配列に、medication_id をキーとして中間テーブルのデータを格納
                $pivotData[$medicationData['medication_id']] = [
                    'taken_dosage' => $medicationData['taken_dosage'] ?? null, // 服用量
                    'is_completed' => $isCompleted, // 服用完了状態
                    'reason_not_taken' => $reasonNotTaken, // 服用しなかった理由
                ];
            }
            // RecordとMedicationの多対多リレーションを確立し、中間テーブルのデータを保存します。
            $record->medications()->attach($pivotData);
        }

        // 成功メッセージと共に内服記録一覧ページにリダイレクト
        return redirect()->route('records.index')->with('success', '内服記録が追加されました。');
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
