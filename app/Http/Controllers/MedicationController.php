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
        //
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
            'side_effect'=>'nullable|string|max:1000',
        ]);
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
