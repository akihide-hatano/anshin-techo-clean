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
        Schema::create('medications', function (Blueprint $table) {
            $table->id('medication_id');
            $table->string('medication_name');
            $table->string('dosage')->nullable();//デフォルトの内服量
            $table->text('notes')->nullable(); // 薬に関するメモ
            $table->text('effect')->nullable(); // 薬の効果
            $table->text('side_effects')->nullable(); // 副作用
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medications');
    }
};
