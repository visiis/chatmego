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
        Schema::create('member_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 等级名称，如：青铜、白银、黄金
            $table->string('icon')->nullable(); // 等级图标
            $table->integer('min_points'); // 最低积分要求
            $table->integer('max_points')->nullable(); // 最高积分要求
            $table->integer('level_order'); // 等级顺序
            $table->text('privileges')->nullable(); // 特权描述（JSON 格式）
            $table->boolean('is_active')->default(true); // 是否启用
            $table->timestamps();
            
            $table->index('min_points');
            $table->index('level_order');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_levels');
    }
};
