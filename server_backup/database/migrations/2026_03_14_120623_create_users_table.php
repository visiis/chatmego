<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::dropIfExists('users');
    Schema::create('users', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->unique();
        $table->string('phone')->unique()->nullable();
        $table->string('password');
        $table->string('avatar')->nullable();
        $table->string('gender')->nullable();
        $table->integer('age')->nullable();
        $table->string('height')->nullable();
        $table->string('weight')->nullable();
        $table->string('hobbies')->nullable();
        $table->string('specialty')->nullable();
        $table->text('love_declaration')->nullable();
        $table->integer('points')->default(0);
        $table->boolean('is_active')->default(true);
        $table->rememberToken();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
