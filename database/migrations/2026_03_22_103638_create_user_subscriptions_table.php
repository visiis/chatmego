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
        Schema::create('user_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->constrained('membership_plans')->onDelete('cascade');
            $table->timestamp('starts_at')->nullable(); // 开始时间
            $table->timestamp('ends_at')->nullable(); // 结束时间
            $table->enum('status', ['active', 'expired', 'cancelled'])->default('active'); // 状态
            $table->integer('price_paid'); // 支付价格（金币）
            $table->timestamp('cancelled_at')->nullable(); // 取消时间
            $table->text('notes')->nullable(); // 备注
            $table->timestamps();
            
            $table->index(['user_id', 'status']);
            $table->index('ends_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_subscriptions');
    }
};
