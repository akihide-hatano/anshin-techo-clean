<?php

namespace App\Http\Controllers;

use App\Models\Medication;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // Ruleファサードを忘れずにインポート
use function Ramsey\Uuid\v1;

class MedicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        //クエリービルダーの初期化
        $query = Medication::query();

        //検索キーワードを取得
        $medicationName = $request->input('medication_name');
        $effect = $request->input('effect');
        $sideEffects = $request->input('side_effects');

        //検索条件をクエリに追加
        if($medicationName){
            $query->where('medication_name','like',"%{$medicationName}%");
        }
        if($effect){
            $query->where('effect','like',"%{$effect}%");
        }
        if($sideEffects){
            $query->where('sideEffects','like',"%{$sideEffects}%");
        }

        // フィルタリングされた結果を取得
        $medications = $query->paginate(9);

        //  dd($medications);
        // dd($effect);

        return view('medications.index', compact('medications','effect','sideEffects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // 薬の新規作成フォームを表示
        return view('medications.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //validationの作成
        $request->validate([
            'medication_name'=>'required|string|max:255|unique:medications,medication_name',
            'dosage'=>'required|string|max:255',
            'notes'=>'nullable|string|max:1000',
            'effect'=>'required|string|max:1000',
            'side_effects'=>'required|string|max:1000',
        ]);
        //databaseへの保存
        Medication::create([
            'medication_name'=>$request->medication_name,
            'dosage'=>$request->dosage,
            'notes'=>$request->notes,
            'effect'=>$request->effect,
            'side_effects' => $request->side_effects,
        ]);


         // 3. リダイレクト (登録後、薬一覧ページへ戻る)
        return redirect()->route('medications.index')
                         ->with('status', '新しい薬が登録されました！'); // フラッシュメッセージ
    }

    /**
     * Display the specified resource.
     */
    public function show(Medication $medication)
    {
        // dd($medication);
        return view('medications.show',compact('medication'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Medication $medication)
    {
        return view('medications.edit',compact('medication'));
    }

    /**
     * Update the specified resource in storage.
     */

public function update(Request $request, Medication $medication)
{
    //validationのルール
    $validatedData = $request->validate([
        'medication_name' => [
            'required',
            'string',
            'max:255',
            Rule::unique('medications', 'medication_name')->ignore($medication->medication_id,'medication_id'),
        ],
        'dosage' => 'required|string|max:255',
        'notes' => 'nullable|string|max:1000',
        'effect' => 'nullable|string|max:1000',
        'side_effects' => 'nullable|string|max:1000',
    ]);

    // バリデーションが成功したら実行される
    $medication->update($validatedData);
    return redirect()->route('medications.show', $medication)
                    ->with('status','薬の情報が更新されました');
}
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Medication $medication)
    {
        $medication->delete();

        return redirect()->route('medications.index')
                        ->with('status','薬の情報を削除しました');
    }
}
