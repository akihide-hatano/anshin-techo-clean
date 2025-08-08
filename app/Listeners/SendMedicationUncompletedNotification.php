<?php

namespace App\Listeners;

use App\Events\MedicationMarkedUncompleted;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendMedicationUncompletedNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(MedicationMarkedUncompleted $event): void
    {
        //イベントで渡されてdataを取得
            $record = $event->record;
            $medication = $event->medication;
            $reasonNotTaken = $event->reasonNotTaken;
            $user = $event->user;
    }
}
