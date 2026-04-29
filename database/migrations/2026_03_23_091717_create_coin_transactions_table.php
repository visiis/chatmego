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
        Schema::create('coin_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('amount'); // 金币数量，可正可负
            $table->integer('balance_after'); // 操作后的余额
            $table->string('type'); // 类型：add(添加), deduct(扣除), recharge(充值), exchange(兑换)
            $table->text('note')->nullable(); // 备注
            $table->foreignId('admin_id')->nullable()->constrained('users')->onDelete('set null'); // 操作的管理员
            $table->timestamps();
            
            $table->index(['user_id', 'type']);
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coin_transactions');
    }
};
