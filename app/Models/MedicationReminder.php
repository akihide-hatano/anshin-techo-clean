<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // BelongsTo リレーションシップのために必要

class MedicationReminder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'patient_name',
        'event_type',
        'message',
        'is_read',
    ];

    /**
     * この通知がどのユーザーに属するかを定義します。
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}