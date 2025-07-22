    <?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Database\Eloquent\Relations\BelongsTo; // この行を追記してください

    class FcmToken extends Model
    {
        use HasFactory;

        // FcmTokenモデルが持つカラムのうち、一括割り当てを許可するもの
        protected $fillable = [
            'user_id',
            'token',
        ];

        /**
         * このFCMトークンが属するユーザーを取得します。
         */
        public function user(): BelongsTo
        {
            return $this->belongsTo(User::class);
        }
    }
    