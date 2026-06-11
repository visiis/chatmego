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
        Schema::create('user_albums', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->comment('用户ID');
            $table->string('name')->comment('相册名称');
            $table->text('description')->nullable()->comment('相册描述');
            $table->tinyInteger('privacy')->default(1)->comment('隐私设置:1公开,0隐藏(需要付费)');
            $table->integer('price')->default(0)->comment('观看所需金币(隐藏时生效)');
            $table->integer('view_count')->default(0)->comment('浏览次数');
            $table->integer('purchase_count')->default(0)->comment('购买次数');
            $table->tinyInteger('status')->default(1)->comment('状态:1启用,0禁用');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_albums');
    }
};
