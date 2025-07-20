<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // 必要に応じて
use Illuminate\Database\Eloquent\Relations\BelongsToMany; // 必要に応じて

class Record extends Model
{
    use HasFactory;

    // プライマリキーをカスタムする場合
    protected $primaryKey = 'record_id';
    public $incrementing = true; // record_idが自動増分の場合

    // タイムスタンプを使用しない場合 (通常は使用)
    // public $timestamps = false;

    protected $fillable = [
        'user_id',
        'timing_tag_id',
        // 必要に応じて他のカラム
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function timingTag(): BelongsTo
    {
        return $this->belongsTo(TimingTag::class, 'timing_tag_id', 'timing_tag_id');
    }

    public function medications(): BelongsToMany
    {
        return $this->belongsToMany(Medication::class, 'record_medication', 'record_id', 'medication_id')
                    ->withPivot('taken_dosage', 'is_completed', 'reason_not_taken') // 中間テーブルのカラム
                    ->withTimestamps(); // 中間テーブルにcreated_at, updated_atがある場合
    }
}