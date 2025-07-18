<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany; // HasManyリレーションシップのために追加

class TimingTag extends Model
{
    use HasFactory;

    protected $table = 'timing_tags';
    protected $primaryKey = 'timing_tag_id';

    // 主キーがint型の場合、この指定が重要です
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'timing_name',
        'base_time',
    ];

    /**
     * この服用タイミングに紐づく服用記録を取得
     */
    public function records(): HasMany
    {
        return $this->hasMany(Record::class, 'timing_tag_id', 'timing_tag_id');
    }
}