<?php

namespace App\Http\Controllers;

use App\Models\Record;
use App\Models\Medication; // Medication モデルを追加
use App\Models\TimingTag;  // TimingTag モデルを追加
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\Auth;

use function Ramsey\Uuid\v1;

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
    public function show(Record $record)
    {
        //レコードの内容がuserであるか確認
        if( $record->user_id !== Auth::id()){
            abort(403,'記事の確認権限がありません');
        }
        $record->load(['medications','timingTag']);

        // ★ここから追加・修正★
        // 各medicationに表示用のプロパティを追加
        $record->medications->each(function ($medication) {
            // pivotテーブルの値を直接プロパティとして追加
            $medication->_is_completed = $medication->pivot->is_completed;
            $medication->_reason_not_taken = $medication->pivot->reason_not_taken;
        });
        // ★ここまで追加・修正★

        return view('records.show',compact('record'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Record $record)
    {
        //ユーザーのレコードであることを確認
        if($record->user_id !== Auth::id()){
            abort(403,'記事の確認権限がありません');
        }
        //内服薬と服用タイミングを取得
        $medications = Medication::orderBy('medication_name')->get();
        $timingTags = TimingTag::orderBy('timing_tag_id','asc')->get();

        // 関連するmedicationsをロードし、ピボットデータをカスタムプロパティに格納
        $record->load(['medications' => function($query) {
            $query->withPivot('taken_dosage', 'is_completed', 'reason_not_taken');
        }]);

        // 各medicationに表示用のプロパティを追加
        $record->medications->each(function ($medication) {
            $medication->_is_completed = $medication->pivot->is_completed;
            $medication->_reason_not_taken = $medication->pivot->reason_not_taken;
        });
        // ★ここを追加★
        return view('records.edit', compact('record', 'medications', 'timingTags'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,Record $record)
    {
        // 認証されたユーザーがこのレコードを所有しているか確認
        if ($record->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        //バリデーションで確認
        $validated = $request->validate([
            'taken_date' => 'required|date',
            'timing_tag_id' => 'required|exists:timing_tags,timing_tag_id',
            'medications' => 'nullable|array',
            'medications.*.medication_id' => 'required|exists:medications,medication_id',
            'medications.*.taken_dosage' => 'nullable|string|max:255',
            'medications.*.is_completed' => 'nullable|boolean',
            'medications.*.reason_not_taken' => 'nullable|string|max:255',
        ]);

        $timingTag = TimingTag::find($validated['timing_tag_id']);
        $baseTime = $timingTag ? $timingTag->base_time : '00:00:00';
        $takenAt = Carbon::parse($validated['taken_date'] . '' .$baseTime);

        $record->update([
            'taken_at'=>$takenAt,
            'timing_tag_id'=>$validated['timing_tag_id'],
        ]);

        // dd($timingTag,$baseTime,$takenAt,$record);
        $pivotData = [];
        if (isset($validated['medications'])) {
            foreach ($validated['medications'] as $medicationData) {
                $isCompleted = isset($medicationData['is_completed']) && $medicationData['is_completed'] === '1';
                $reasonNotTaken = null;
                if (!$isCompleted) {
                    $reasonNotTaken = $medicationData['reason_not_taken'] ?? null;
                }

                $pivotData[$medicationData['medication_id']] = [
                    'taken_dosage' => $medicationData['taken_dosage'] ?? null,
                    'is_completed' => $isCompleted,
                    'reason_not_taken' => $reasonNotTaken,
                ];
            }
        }
        $record->medications()->sync($pivotData);

        return redirect()->route('records.show', $record)->with('success', '内服記録が更新されました。');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Record $record)
    {
        if( $record->user_id !== Auth::id()){
            abort(403,'記事削除の権限がありません。');
        }
            $record->delete();
            return redirect()->route('records.index')->with('success', '内服記録が削除されました。');
    }

    public function calendar(){
        return view('records.calendar');
    }

      /**
     * Get calendar events (medication records) for FullCalendar.
     * このメソッドがAPIエンドポイントとして機能します。
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCalendarEvents(Request $request)
    {
        $user = Auth::user();

        $start = Carbon::parse($request->input('start'));
        $end = Carbon::parse($request->input('end'));

        $records = Record::where('user_id', $user->id)
                         ->whereBetween('taken_at', [$start, $end])
                         ->with(['medications.pivot', 'timingTag'])
                         ->get();

        $events = [];

        foreach ($records as $record) {
            // ★★★ ここから追加/修正 ★★★
            // その記録に未完了の薬があるかチェック
            $recordHasUncompleted = $record->medications->contains(function ($medication) {
                return !$medication->pivot->is_completed;
            });

            // イベントのタイトルに「⚪︎」または「×」を追加
            $statusSymbol = $recordHasUncompleted ? '× ' : '⚪︎ '; // 未完了があれば×、全て完了なら⚪︎

            // 記録全体としてイベントを作成（各薬ごとではなく、日ごとの記録として集約）
            // 複数の薬がある場合でも、その日の記録全体として一つのイベントにするため、
            // その日の最初の薬の情報を代表として使うか、または記録全体を表現するタイトルにします。
            // ここでは、その記録の服用タイミングと状態をタイトルにします。
            $title = $statusSymbol . $record->timingTag->timing_name;

            // 各薬の詳細を説明に含める
            $medicationDetails = $record->medications->map(function ($medication) {
                $detail = $medication->medication_name;
                if ($medication->pivot->taken_dosage) {
                    $detail .= ' (' . $medication->pivot->taken_dosage . ')';
                }
                if (!$medication->pivot->is_completed) {
                    $detail .= ' - 未完了';
                    if ($medication->pivot->reason_not_taken) {
                        $detail .= ' (' . $medication->pivot->reason_not_taken . ')';
                    }
                } else {
                    $detail .= ' - 完了';
                }
                return $detail;
            })->implode("\n"); // 各薬の詳細を改行で結合

            $description = "服用記録:\n" . $medicationDetails;

            // イベントの色を完了/未完了で変更 (これは以前のままでOK)
            $color = $recordHasUncompleted ? '#FFC107' : '#4CAF50'; // 未完了があれば黄色、全て完了なら緑

            $events[] = [
                'id' => $record->record_id, // 記録IDをイベントIDとして使用
                'title' => $title, // カレンダーに表示されるタイトル
                'start' => $record->taken_at->toDateTimeString(), // イベントの開始日時
                'extendedProps' => [ // カスタムデータ
                    'description' => $description,
                    'record_has_uncompleted' => $recordHasUncompleted, // 未完了フラグ
                    'timing_name' => $record->timingTag->timing_name,
                    'medication_details' => $medicationDetails, // 各薬の詳細
                ],
                'backgroundColor' => $color,
                'borderColor' => $color,
                'url' => route('records.show', $record->record_id), // クリック時の遷移先URL
            ];
            // ★★★ 修正ここまで ★★★
        }

        return response()->json($events);
    }
}
