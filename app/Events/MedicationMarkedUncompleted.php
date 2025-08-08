<?php

namespace App\Events;

use App\Models\Medication;
use App\Models\Record;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MedicationMarkedUncompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Record $record;
    public Medication $medication;
    public ?string $reasonNotTaken;
    public User $user;
    
    // ★追加: メールに表示する詳細情報をプロパティとして追加
    public string $medicationName;
    public string $takenAt;

    /**
     * Create a new event instance.
     */
    public function __construct(Record $record, Medication $medication, ?string $reasonNotTaken, User $user)
    {
        $this->record = $record;
        $this->medication = $medication;
        $this->reasonNotTaken = $reasonNotTaken;
        $this->user = $user;
        
        // ★追加: 渡されたオブジェクトから詳細情報を抽出してプロパティにセット
        $this->medicationName = $medication->medication_name;
        $this->takenAt = $record->taken_at->format('Y年m月d日');
    }
}