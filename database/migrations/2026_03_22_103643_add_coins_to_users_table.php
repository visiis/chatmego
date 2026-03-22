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
        Schema::table('users', function (Blueprint $table) {
            $table->integer('coins')->default(0)->after('points'); // 金币余额
            $table->integer('total_coins_spent')->default(0)->after('coins'); // 累计消费金币
            $table->integer('total_coins_recharged')->default(0)->after('total_coins_spent'); // 累计充值金币
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
