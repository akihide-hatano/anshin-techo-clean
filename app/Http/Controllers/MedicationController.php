<?php

namespace App\Http\Controllers;

use App\Models\Medication;
use Illuminate\Http\Request;


class MedicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $medications = Medication::all();
        // dd($medications);

        return view('medications.index', compact('medications'));
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
            'dosage'=>'nullable|string|max:255',
            'notes'=>'nullable|string|max:1000',
            'effect'=>'nullable|string|max:1000',
            'side_effects'=>'nullable|string|max:1000',
        ]);
        //databaseへの保存
        Medication::create([
            'medication_name'=>$request->medication_name,
            'dosage'=>$request->dosage,
            'notes'=>$request->notes,
            'effect'=>$request->effect,
            'side_effects' => $request->side_effects,
        ]);

        // dd($request);

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
        return view('medication.show',compact('medication'));
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
