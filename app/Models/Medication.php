<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medication extends Model
{
    use HasFactory;

    protected $primaryKey = 'medication_id'; // 主キーのカラム名を指定
    protected $keyType = 'int'; // 主キーの型を指定（必要であれば）

    protected $fillable = [
        'medication_name',
        'dosage',
        'notes',
        'effects',
        'side_effects',
    ];

    /**
     * この薬が関連付けられている複数の服用記録を取得（多対多）
     */
    public function records()
    {
        // belongsToMany(関連モデル, 中間テーブル名, 自身の外部キー, 相手の外部キー)
        // withPivot() で中間テーブルに追加したカラムを指定することで、それらの値も取得できるようになる
        // withTimestamps() で中間テーブルの created_at と updated_at も自動的に扱われるようになる
        return $this->belongsToMany(Record::class, 'record_medication', 'medication_id', 'record_id')
                    ->withPivot(['taken_dosage', 'is_completed', 'reason_not_taken'])
                    ->withTimestamps();
    }
}