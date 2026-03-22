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
        Schema::create('gift_redemptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // 用户 ID
            $table->foreignId('gift_id')->constrained()->onDelete('cascade'); // 礼物 ID
            $table->foreignId('user_gift_id')->constrained('user_gifts')->onDelete('cascade'); // 用户礼物 ID
            $table->string('recipient_name'); // 收件人姓名
            $table->string('phone'); // 电话
            $table->text('address'); // 地址
            $table->enum('status', ['pending', 'shipped', 'delivered'])->default('pending'); // 状态
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gift_redemptions');
    }
};
