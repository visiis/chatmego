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
        Schema::create('user_gifts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // 用户 ID
            $table->foreignId('gift_id')->constrained()->onDelete('cascade'); // 礼物 ID
            $table->integer('quantity')->default(1); // 数量
            $table->boolean('is_redeemed')->default(false); // 是否已兑换（仅实体礼物）
            $table->timestamps();
            
            $table->index(['user_id', 'is_redeemed']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_gifts');
    }
};
