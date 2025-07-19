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
        Schema::create('record_medication', function (Blueprint $table) {
            // ユーザーID (usersテーブルへの外部キー)
            $table->foreignId('record_id')->constrained('records', 'id')->onDelete('cascade');
            $table->foreignId('medications_id')->constrained('medications', 'id')->onDelete('cascade');

            // この服用記録における、この薬の実際の服用量
            $table->string('taken_dosage')->nullable();

            // 個々の薬の服用完了状態と理由を中間テーブルで管理
            $table->boolean('is_completed')->default(false);
            $table->text('reason_not_taken')->nullable();

            // 同じrecord_idとmedication_idの組み合わせが重複しないように複合ユニークインデックスを設定
            $table->unique(['record_id', 'medication_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('record_medication');
    }
};
