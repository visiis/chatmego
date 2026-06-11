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
        Schema::create('album_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('album_id')->constrained('user_albums')->onDelete('cascade')->comment('相册ID');
            $table->string('image_url')->comment('图片URL(图床地址)');
            $table->string('thumbnail_url')->comment('缩略图URL(图床地址)');
            $table->string('title')->nullable()->comment('图片标题');
            $table->text('description')->nullable()->comment('图片描述');
            $table->integer('sort_order')->default(0)->comment('排序');
            $table->tinyInteger('status')->default(1)->comment('状态:1启用,0禁用');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('album_photos');
    }
};
