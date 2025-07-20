<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany; // ★この行が重要です！

class User extends Authenticatable
{
    use HasFactory, Notifiable; // HasApiTokens は不要なので削除

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * このユーザーに紐づく内服記録を取得
     */
    public function records(): HasMany // ★このメソッド定義が重要です！
    {
        return $this->hasMany(Record::class, 'user_id', 'id');
    }

    /**
     * このユーザーに紐づく薬を取得 (Medicationモデルとのリレーション)
     */
    public function medications(): HasMany
    {
        return $this->hasMany(Medication::class, 'user_id', 'id');
    }
}