<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medication_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // usersテーブルへの外部キー
            $table->string('patient_name')->nullable(); // 患者名（誰の内服忘れかを示す）
            $table->string('event_type'); // 例: 'forgotten_medication'
            $table->text('message')->nullable(); // 通知メッセージの本文（任意）
            $table->boolean('is_read')->default(false); // 既読/未読フラグ（任意）
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medication_reminders');
    }
};