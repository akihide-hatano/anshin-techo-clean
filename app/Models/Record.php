<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // BelongsToリレーションシップのために追加

class Record extends Model
{
    use HasFactory;

    protected $table = 'records';
    protected $primaryKey = 'record_id';
    public $incrementing = true;
    protected $keyType = 'int'; // 'record_id' がbigIncrementsなのでintでOK

    protected $fillable = [
        'user_id',
        'medication_id',
        'timing_tag_id',
        'is_completed',
        'taken_dosage',
        'taken_at',
        'reason_not_taken',
        'content',
    ];

    // 日付として扱いたいカラムを指定 (Carbonインスタンスとして取得できるようになる)
    protected $casts = [
        'is_completed' => 'boolean',
        'taken_at' => 'datetime',
    ];

    /**
     * この服用記録を所有するユーザーを取得
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * この服用記録に関連する薬を取得
     */
    public function medication(): BelongsTo
    {
        return $this->belongsTo(Medication::class, 'medication_id', 'medication_id');
    }

    /**
     * この服用記録に関連する服用タイミングを取得
     */
    public function timingTag(): BelongsTo
    {
        return $this->belongsTo(TimingTag::class, 'timing_tag_id', 'timing_tag_id');
    }
}