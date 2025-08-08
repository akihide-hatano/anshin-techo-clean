<?php

namespace App\Http\Controllers;

use App\Events\MedicationMarkedUncompleted; // ★追加
use App\Models\Medication;
use App\Models\Record;
use App\Models\RecordMedication;
use App\Models\TimingTag;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
            ->orderBy('taken_at', 'desc')
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
        $medications = Medication::orderBy('medication_name')->get();

        // 服用タイミングはこれまで通り取得
        $timingTags = TimingTag::orderBy('timing_tag_id', 'asc')->get();

        return view('records.create', compact('medications', 'timingTags'));
    }

/**
     * Store a newly created resource in storage.
     */
public function store(Request $request)
    {
        Log::info('DEBUG-STORE: store() method started.');

        try {
            $validated = $request->validate([
                'taken_date' => 'required|date',
                'timing_tag_id' => 'required|exists:timing_tags,timing_tag_id',
                'medications' => 'required|array|min:1',
                'medications.*.medication_id' => 'required|exists:medications,medication_id',
                'medications.*.taken_dosage' => 'nullable|string|max:255',
                'medications.*.is_completed' => 'nullable|boolean',
                'medications.*.reason_not_taken' => 'nullable|string|max:255',
            ]);

            Log::info('DEBUG-STORE: Validation successful. Request data: ' . json_encode($validated));

            $timingTag = TimingTag::find($validated['timing_tag_id']);
            $baseTime = $timingTag ? $timingTag->base_time : '00:00:00';
            $takenAt = Carbon::parse($validated['taken_date'] . ' ' . $baseTime);

            /** @var \App\Models\User $user */
            $user = Auth::user();

            $record = $user->records()->create([
                'taken_at' => $takenAt,
                'timing_tag_id' => $validated['timing_tag_id'],
            ]);

            Log::info('DEBUG-STORE: New record created with ID ' . $record->record_id);

            if (isset($validated['medications'])) {
                $pivotData = [];
                foreach ($validated['medications'] as $medicationData) {
                    $medicationId = $medicationData['medication_id'];
                    $isCompleted = isset($medicationData['is_completed']) && $medicationData['is_completed'] === '1';
                    $reasonNotTaken = null;
                    if (!$isCompleted) {
                        $reasonNotTaken = $medicationData['reason_not_taken'] ?? null;
                        
                        $medication = Medication::find($medicationId);
                        if ($medication) {
                            Log::info("DEBUG-STORE-COND: Condition met for event dispatching for Medication ID {$medicationId} (isCompleted: false).");
                            event(new MedicationMarkedUncompleted($record, $medication, $reasonNotTaken, Auth::user()));
                            Log::info("Medication marked uncompleted event dispatched from store() for Record ID {$record->record_id}, Medication ID {$medicationId}");
                        }
                    }
                    
                    $pivotData[$medicationId] = [
                        'taken_dosage' => $medicationData['taken_dosage'] ?? null,
                        'is_completed' => $isCompleted,
                        'reason_not_taken' => $reasonNotTaken,
                    ];
                }
                Log::info('DEBUG-STORE: Starting sync() for pivot table. Pivot data: ' . json_encode($pivotData));
                $record->medications()->sync($pivotData);
            }
            Log::info('DEBUG-STORE: store() method finished successfully.');

            return redirect()->route('records.index')->with('success', '内服記録が追加されました。');
        } catch (\Exception $e) {
            Log::error('Unexpected Error in RecordController@store: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', '予期せぬエラーが発生しました。しばらくしてから再度お試しください。');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Record $record)
    {
        //レコードの内容がuserであるか確認
        if ($record->user_id !== Auth::id()) {
            abort(403, '記事の確認権限がありません');
        }
        $record->load(['medications', 'timingTag']);

        // 各medicationに表示用のプロパティを追加
        $record->medications->each(function ($medication) {
            // pivotテーブルの値を直接プロパティとして追加
            $medication->_is_completed = $medication->pivot->is_completed;
            $medication->_reason_not_taken = $medication->pivot->reason_not_taken;
        });

        return view('records.show', compact('record'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Record $record)
    {
        //ユーザーのレコードであることを確認
        if ($record->user_id !== Auth::id()) {
            abort(403, '記事の確認権限がありません');
        }
        //内服薬と服用タイミングを取得
        $medications = Medication::orderBy('medication_name')->get();
        $timingTags = TimingTag::orderBy('timing_tag_id', 'asc')->get();

        // 関連するmedicationsをロードし、ピボットデータをカスタムプロパティに格納
        $record->load(['medications' => function ($query) {
            $query->withPivot('taken_dosage', 'is_completed', 'reason_not_taken');
        }]);

        // 各medicationに表示用のプロパティを追加
        $record->medications->each(function ($medication) {
            $medication->_is_completed = $medication->pivot->is_completed;
            $medication->_reason_not_taken = $medication->pivot->reason_not_taken;
        });

        return view('records.edit', compact('record', 'medications', 'timingTags'));
    }

/*
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
                $takenAt = Carbon::parse($validated['taken_date'] . ' ' . $baseTime);

                $record->update([
                    'taken_at' => $takenAt,
                    'timing_tag_id' => $validated['timing_tag_id'],
                ]);

                $pivotData = [];
                $oldPivotData = $record->medications->keyBy('medication_id')->map(function ($med) {
                    return [
                        'is_completed' => (bool) $med->pivot->is_completed,
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

                        $wasCompleted = $oldPivotData->get($medicationId)['is_completed'] ?? true;

                        if (!$isCompleted && $wasCompleted) {
                            $medication = Medication::find($medicationId);
                            if ($medication) {
                                Log::info("DEBUG: Condition met for event dispatching for Medication ID {$medicationId}.");
                                event(new MedicationMarkedUncompleted($record, $medication, $reasonNotTaken, Auth::user()));
                                Log::info("Medication marked uncompleted event dispatched for Record ID {$record->record_id}, Medication ID {$medicationId}");
                            }
                        }
                    }
                }
                $record->medications()->sync($pivotData);

                return redirect()->route('records.show', $record)->with('success', '内服記録が更新されました。');
            } catch (\Exception $e) {
                Log::error('Unexpected Error in RecordController@update: ' . $e->getMessage());
                return redirect()->back()->withInput()->with('error', '予期せぬエラーが発生しました。しばらくしてから再度お試しください。');
            }
        }
    }