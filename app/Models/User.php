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
        'role',
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
     * ユーザーが管理者かどうかをチェックするヘルパーメソッド
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * ユーザーが家族メンバーかどうかをチェックするヘルパーメソッド (例)
     */
    public function isFamilyMember(): bool
    {
        return $this->role === 'family';
    }

    /**
     * このユーザーが持つFCMトークンを取得します。
     */
    public function fcmTokens(): HasMany
    {
        return $this->hasMany(FcmToken::class, 'user_id', 'id');
    }

}