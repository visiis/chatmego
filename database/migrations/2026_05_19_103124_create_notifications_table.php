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
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // 接收通知的用户
            $table->string('type'); // 通知类型：friend_request, message, like, comment等
            $table->unsignedBigInteger('from_user_id')->nullable(); // 发送通知的用户
            $table->text('data')->nullable(); // 额外数据（JSON格式）
            $table->timestamp('read_at')->nullable(); // 已读时间
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('from_user_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
