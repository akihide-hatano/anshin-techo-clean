    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        /**
         * Run the migrations.
         * マイグレーションを実行してテーブルを作成します。
         */
        public function up(): void
        {
            Schema::create('fcm_tokens', function (Blueprint $table) {
                $table->id(); // プライマリキー（自動インクリメント）
                $table->foreignId('user_id')->constrained()->onDelete('cascade'); // usersテーブルへの外部キー
                $table->string('token', 255)->unique(); // FCMトークン（ユニーク）
                $table->timestamps(); // created_at と updated_at カラム
            });
        }

        /**
         * Reverse the migrations.
         * マイグレーションをロールバックしてテーブルを削除します。
         */
        public function down(): void
        {
            Schema::dropIfExists('fcm_tokens');
        }
    };
    