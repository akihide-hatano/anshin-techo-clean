<?php

namespace App\Http\Controllers;

use App\Models\Medication;
use App\Models\MedicationReminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MedicationReminderController extends Controller
{
    public function mardAsRead( MedicationReminder $medicationReminder){

    if( $medicationReminder->user_id !== Auth::id()){
            abort(403,'権限がありません');
        }

        $medicationReminder->update(['is_read'=>true]);

        return redirect()->back()->with('status','通知を既読にしました。');
    }
}
