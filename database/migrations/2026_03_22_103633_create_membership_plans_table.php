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
        Schema::create('membership_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 会员名称：基础会员、VIP、SVIP
            $table->string('code')->unique(); // 会员代码：basic, vip, svip
            $table->integer('price'); // 价格（金币/月）
            $table->integer('duration_days')->default(30); // 有效期（天）
            $table->text('privileges')->nullable(); // 特权描述（JSON 格式）
            $table->string('icon')->nullable(); // 图标
            $table->string('badge_color')->nullable(); // 徽章颜色
            $table->boolean('is_active')->default(true); // 是否启用
            $table->integer('sort_order')->default(0); // 排序
            $table->timestamps();
            
            $table->index('code');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_plans');
    }
};
