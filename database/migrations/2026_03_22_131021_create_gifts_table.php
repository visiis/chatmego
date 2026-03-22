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
        Schema::create('gifts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 礼物名称
            $table->enum('type', ['virtual', 'physical']); // virtual:虚拟礼物，physical:实体礼物
            $table->enum('price_type', ['activity_points', 'coins']); // activity_points:活跃度，coins:金币
            $table->integer('price'); // 价格
            $table->string('image')->nullable(); // 礼物图片
            $table->text('description')->nullable(); // 描述
            $table->boolean('is_active')->default(true); // 是否启用
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gifts');
    }
};
