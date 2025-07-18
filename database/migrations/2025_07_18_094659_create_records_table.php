<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('records', function (Blueprint $table) {
            $table->id('record_id'); // 主キー (bigint, auto-increment)

            // ユーザーID (usersテーブルへの外部キー)
            $table->foreignId('user_id')->constrained('users', 'id')->onDelete('cascade');

            // 薬ID (medicationsテーブルへの外部キー)
            $table->foreignId('medication_id')->constrained('medications', 'medication_id')->onDelete('cascade');
            // onDelete('cascade') は、薬が削除されたら関連するレコードも削除します。

            // タイミングタグID (timing_tagsテーブルへの外部キー)
            $table->unsignedInteger('timing_tag_id'); // timing_tags.timing_tag_idがint型のため
            $table->foreign('timing_tag_id')->references('timing_tag_id')->on('timing_tags')->onDelete('restrict');
            // onDelete('restrict') は、関連するレコードがある場合、timing_tagの削除を許可しません。

            // その他のカラム
            $table->boolean('is_completed')->default(false); // 服用完了したか（デフォルトは未完了）
            $table->string('taken_dosage')->nullable(); // 実際に服用した量（任意）
            $table->timestamp('taken_at')->nullable(); // 実際に服用した時刻または記録日時（任意）
            $table->text('reason_not_taken')->nullable(); // 服用しなかった理由（任意）
            $table->text('content')->nullable(); // 記録に関する自由記述（メモなど、ER図に合わせて追加）

            $table->timestamps(); // created_at, updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('records');
    }
};