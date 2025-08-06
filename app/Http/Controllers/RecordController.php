<?php

namespace App\Http\Controllers;

use App\Models\Record;
use App\Models\Medication; // Medication モデルを追加
use App\Models\TimingTag;  // TimingTag モデルを追加
use App\Models\RecordMedication;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Database\QueryException;
use App\Events\MedicationMarkedUncompleted; // ★この行を追加★


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
            'taken_date' => 'required|date',
            'timing_tag_id' => 'required|exists:timing_tags,timing_tag_id',
            'medications' => 'required|array|min:1',
            'medications.*.medication_id' => 'required|exists:medications,medication_id',
            'medications.*.taken_dosage' => 'nullable|string|max:255',
            'medications.*.is_completed' => 'nullable|boolean',
            'medications.*.reason_not_taken' => 'nullable|string|max:255',
        ]);

        $timingTag = TimingTag::find($validated['timing_tag_id']);
        $baseTime = $timingTag ? $timingTag->base_time : '00:00:00';
        $takenAt = Carbon::parse($validated['taken_date'] . ' ' . $baseTime);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        $record = $user->records()->create([
            'taken_at' => $takenAt,
            'timing_tag_id' => $validated['timing_tag_id'],
        ]);

        // ★★★ ここから修正 ★★★
        if (isset($validated['medications'])) {
            foreach ($validated['medications'] as $medicationData) {
                $isCompleted = isset($medicationData['is_completed']) && $medicationData['is_completed'] === '1';

                // ★★★ ここにdd()を追加 ★★★
                // $medicationData の中身と、$isCompleted がどうなっているかを確認
                // dd($medicationData, $isCompleted);
                // ★★★ dd()追加ここまで ★★★

                $reasonNotTaken = null;
                if (!$isCompleted) {
                    $reasonNotTaken = $medicationData['reason_not_taken'] ?? null;
                }
                // RecordMedication モデルを直接作成することで、created イベントが発火する
                RecordMedication::create([
                    'record_id' => $record->record_id, // 作成したRecordのID
                    'medication_id' => $medicationData['medication_id'],
                    'taken_dosage' => $medicationData['taken_dosage'] ?? null,
                    'is_completed' => $isCompleted,
                    'reason_not_taken' => $reasonNotTaken,
                ]);
            }
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
         * 内服記録を更新し、未完了になった薬があれば管理者通知イベントを発火します。
         */
        public function update(Request $request, Record $record)
        {
            try {
                if ($record->user_id !== Auth::id()) {
                    abort(403, 'Unauthorized action.');
                }

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
                $takenAt = Carbon::parse($validated['taken_date'] . ' ' .$baseTime);

                $record->update([
                    'taken_at' => $takenAt,
                    'timing_tag_id' => $validated['timing_tag_id'],
                ]);

                $pivotData = [];
                // 更新前に既存のピボットデータを取得しておく (変更を検出するため)
                $oldPivotData = $record->medications->keyBy('medication_id')->map(function ($med) {
                    return [
                        'is_completed' => (bool)$med->pivot->is_completed,
                        'reason_not_taken' => $med->pivot->reason_not_taken,
                    ];
                });

                if (isset($validated['medications'])) {
                    foreach ($validated['medications'] as $medicationData) {
                        $medicationId = $medicationData['medication_id'];
                        $isCompleted = filter_var($medicationData['is_completed'] ?? false, FILTER_VALIDATE_BOOLEAN);
                        $reasonNotTaken = null;
                        if (!$isCompleted) {
                            $reasonNotTaken = $medicationData['reason_not_taken'] ?? null;
                        }

                        $pivotData[$medicationId] = [
                            'taken_dosage' => $medicationData['taken_dosage'] ?? null,
                            'is_completed' => $isCompleted,
                            'reason_not_taken' => $reasonNotTaken,
                        ];

                        // ★★★ ここから追加: 未完了イベントの発火ロジック ★★★
                        // 薬の完了状態が「完了」から「未完了」に変わった場合、または新規で「未完了」の場合にイベントを発火
                        $wasCompleted = $oldPivotData->get($medicationId)['is_completed'] ?? true;

                        if (!$isCompleted && $wasCompleted) {
                            $medication = Medication::find($medicationId);
                            if ($medication) {
                                // MedicationMarkedUncompleted イベントをディスパッチ
                                event(new MedicationMarkedUncompleted($record, $medication, $reasonNotTaken, Auth::user()));
                                Log::info("Medication marked uncompleted event dispatched for Record ID {$record->record_id}, Medication ID {$medicationId}");
                            }
                        }
                        // ★★★ 修正ここまで ★★★
                    }
                }
                // 中間テーブルの同期 (sync)
                $record->medications()->sync($pivotData);

                return redirect()->route('records.show', $record)->with('success', '内服記録が更新されました。');

            } catch (QueryException $e) {
                Log::error('Database Error in RecordController@update: ' . $e->getMessage());
                return redirect()->back()->withInput()->with('error', 'データベースエラーが発生しました。入力内容を確認してください。');
            } catch (Exception $e) {
                Log::error('Unexpected Error in RecordController@update: ' . $e->getMessage());
                return redirect()->back()->withInput()->with('error', '予期せぬエラーが発生しました。しばらくしてから再度お試しください。');
            }
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCalendarEvents(Request $request)
    {
        try {
            $user = Auth::user();

            $start = Carbon::parse($request->input('start'));
            $end = Carbon::parse($request->input('end'));

            // ユーザーに紐づく、指定期間内の内服記録を取得
            // medications と timingTag リレーションをEager Load
            $records = Record::where('user_id', $user->id)
                            ->whereBetween('taken_at', [$start, $end])
                            ->with(['medications', 'timingTag'])
                            ->get();

            $dailyRecords = [];
            // ★★★ ここから修正: 日付ごとに記録をグループ化し、完了状態を集約 ★★★
            foreach ($records as $record) {
                Log::info(json_encode($record));
                if (!$record->taken_at instanceof Carbon) {
                    Log::warning("Record ID {$record->record_id} has invalid taken_at: " . $record->taken_at);
                    continue;
                }

                $date = $record->taken_at->toDateString(); // 'YYYY-MM-DD' 形式の日付文字列を取得

                // その記録に未完了の薬が一つでもあれば true
                $recordHasUncompleted = $record->medications->contains(function ($medication) {
                    return !$medication->pivot->is_completed;
                });

                // 日付ごとの集約データを作成または更新
                // その日の最初の記録であれば初期化
                if (!isset($dailyRecords[$date])) {
                    $dailyRecords[$date] = [
                        'date' => $date,
                        'has_uncompleted_meds' => false, // 初期値は全て完了と仮定
                        'record_ids' => [], // その日のレコードIDを保持
                        'timing_names' => [], // その日の服用タイミング名を保持
                    ];
                }

                // その日のいずれかの記録に未完了の薬があれば、その日の総合ステータスを未完了にする
                if ($recordHasUncompleted) {
                    $dailyRecords[$date]['has_uncompleted_meds'] = true;
                }

                $dailyRecords[$date]['record_ids'][] = $record->record_id;
                $dailyRecords[$date]['timing_names'][] = $record->timingTag ? $record->timingTag->timing_name : '不明';
            }

            $events = [];

            // 集約された日付ごとのデータからイベントを生成
            foreach ($dailyRecords as $date => $dailyData) {
                $statusSymbol = $dailyData['has_uncompleted_meds'] ? '×' : '⚪︎';
                $color = $dailyData['has_uncompleted_meds'] ? '#FFC107' : '#4CAF50'; // 未完了があれば黄色、全て完了なら緑

                // イベントのタイトルは「⚪︎」または「×」と、その日の服用タイミングの概要
                // 例: ⚪︎ (朝食後, 夕食前)
                $timingSummary = count($dailyData['timing_names']) > 1
                               ? '(' . implode(', ', array_unique($dailyData['timing_names'])) . ')'
                               : ($dailyData['timing_names'][0] ?? '記録あり'); // 1つだけならそのまま、なければ「記録あり」

                $title = $statusSymbol . ' ' . $timingSummary;

                // イベントのURLは、その日の最初の記録の詳細ページにリンクするか、
                // またはその日の記録一覧ページ（もしあれば）にリンクするのが良いでしょう。
                // ここでは、その日の最初のrecord_idにリンクします。
                $firstRecordId = !empty($dailyData['record_ids']) ? $dailyData['record_ids'][0] : null;
                $url = $firstRecordId ? route('records.show', $firstRecordId) : null;

                $events[] = [
                    'id' => $date, // 日付をイベントIDとして使用
                    'title' => $title,
                    'start' => $date, // 日付のみを設定
                    'allDay' => true, // 終日イベントとして扱う
                    'extendedProps' => [
                        'date' => $date,
                        'has_uncompleted_meds' => $dailyData['has_uncompleted_meds'],
                        'record_ids' => $dailyData['record_ids'],
                        'timing_names' => $dailyData['timing_names'],
                        'description' => 'この日の服用記録: ' . implode(', ', array_unique($dailyData['timing_names'])) . ($dailyData['has_uncompleted_meds'] ? ' (未完了あり)' : ' (全て完了)'),
                    ],
                    'backgroundColor' => $color,
                    'borderColor' => $color,
                    'url' => $url,
                ];
            }
            // ★★★ 修正ここまで ★★★

            return response()->json($events);

        } catch (Exception $e) {
            Log::error('Error in RecordController@getCalendarEvents: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());

            return response()->json([], 500);
        }
    }
}
