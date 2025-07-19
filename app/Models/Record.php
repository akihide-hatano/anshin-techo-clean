<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // BelongsToリレーションシップのために追加
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Record extends Model
{
    use HasFactory;

    protected $table = 'records';
    protected $primaryKey = 'record_id';
    public $incrementing = true;
    protected $keyType = 'int'; // 'record_id' がbigIncrementsなのでintでOK

    protected $fillable = [
        'user_id',
        'timing_tag_id',
    ];

    /**
     * この服用記録を所有するユーザーを取得
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * この服用記録に関連する服用タイミングを取得
     */
    public function timingTag(): BelongsTo
    {
        return $this->belongsTo(TimingTag::class, 'timing_tag_id', 'timing_tag_id');
    }

        /**
     * この服用記録に関連する複数の薬を取得（多対多）
     */
    public function medications(): BelongsToMany
    {
        // belongsToMany(関連モデル, 中間テーブル名, 自身の外部キー, 相手の外部キー)
        return $this->belongsToMany(Medication::class, 'record_medication', 'record_id', 'medication_id')
                    ->withPivot(['taken_dosage', 'is_completed', 'reason_not_taken']) // 中間テーブルのカラムを指定
                    ->withTimestamps(); // 中間テーブルの created_at, updated_at も取得
    }
}