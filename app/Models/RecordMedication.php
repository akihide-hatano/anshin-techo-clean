<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecordMedication extends Model
{
    use HasFactory;

    // テーブル名を指定（Laravelはデフォルトでクラス名の複数形をテーブル名とするが、
    protected $table = 'record_medication';
    // 主キーのカラム名を指定（デフォルトは'id'だが、カスタム名なので指定）
    protected $primaryKey = 'record_medication_id';
    // 主キーが自動増分ではない場合（この場合は自動増分なので通常は不要だが、明示しても良い）
    public $incrementing = true;
    // 主キーの型（デフォルトはint/bigintだが、明示しても良い）
    protected $keyType = 'int'; // or 'bigint' depending on your migration

    protected $fillable = [
        'record_id',
        'medication_id',
        'taken_dosage',
        'is_completed',
        'reason_not_taken',
    ];

    // リレーションシップ

    /**
     * この中間レコードが属するRecord（服用記録）を取得
     */
    public function record()
    {
        return $this->belongsTo(Record::class, 'record_id', 'record_id');
    }

    /**
     * この中間レコードが属するMedication（薬）を取得
     */
    public function medication()
    {
        return $this->belongsTo(Medication::class, 'medication_id', 'medication_id');
    }
}