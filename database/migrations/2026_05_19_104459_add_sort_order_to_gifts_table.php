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
        Schema::table('gifts', function (Blueprint $table) {
            $table->integer('sort_order')->default(0)->after('is_active');
        });
        
        // 为现有礼物设置默认排序
        DB::table('gifts')->orderBy('id')->get()->each(function ($gift, $index) {
            DB::table('gifts')->where('id', $gift->id)->update(['sort_order' => $index + 1]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('gifts', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });
    }
};
