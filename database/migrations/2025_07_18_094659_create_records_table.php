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

            // タイミングタグID (timing_tagsテーブルへの外部キー)
            $table->unsignedInteger('timing_tag_id'); // timing_tags.timing_tag_idがint型のため
            $table->foreign('timing_tag_id')->references('timing_tag_id')->on('timing_tags')->onDelete('restrict');
            // onDelete('restrict') は、関連するレコードがある場合、timing_tagの削除を許可しません。

            // その他のカラム
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