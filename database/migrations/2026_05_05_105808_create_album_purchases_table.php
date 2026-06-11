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
        Schema::create('album_purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('album_id')->constrained('user_albums')->onDelete('cascade')->comment('相册ID');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade')->comment('购买者ID');
            $table->foreignId('seller_id')->constrained('users')->onDelete('cascade')->comment('相册所有者ID');
            $table->integer('price')->comment('购买价格(金币)');
            $table->integer('seller_earned')->comment('卖家获得金币(50%)');
            $table->integer('platform_earned')->comment('平台获得金币(50%)');
            $table->timestamp('expires_at')->nullable()->comment('有效期截止时间');
            $table->tinyInteger('status')->default(1)->comment('状态:1有效,0无效');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('album_purchases');
    }
};
