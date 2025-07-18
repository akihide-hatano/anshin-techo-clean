<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // HasManyリレーションシップのために追加

class Medication extends Model
{
    use HasFactory;

    // テーブル名を指定（モデル名が単数形なのでLaravelは自動で'medications'と推測しますが、明示的に指定）
    protected $table = 'medications';

    // 主キーを指定（Laravelのデフォルトは'id'ですが、今回は'medication_id'）
    protected $primaryKey = 'medication_id';

    // 主キーが自動増分であるか（デフォルトはtrueなので省略可能ですが、明示的に）
    public $incrementing = true;

    // 主キーの型を指定（Laravelのデフォルトはintですが、今回はbigIncrementsなのでbigintになりますが、念のため）
    protected $keyType = 'int'; // または 'string' if UUID, 'int' for auto-incrementing bigints

    // 一括代入を許可するカラム
    protected $fillable = [
        'medication_name',
        'dosage',
        'notes',
        'effect',
        'side_effects',
    ];

    /**
     * この薬に紐づく服用記録を取得
     */
    public function records(): HasMany
    {
        return $this->hasMany(Record::class, 'medication_id', 'medication_id');
    }
}