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
            $table->string('easemob_username')->nullable()->after('password');
            $table->string('easemob_password')->nullable()->after('easemob_username');
            $table->string('easemob_uuid')->unique()->nullable()->after('easemob_password');
            $table->boolean('is_online')->default(false)->after('easemob_uuid');
            $table->timestamp('last_seen_at')->nullable()->after('is_online');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['easemob_username', 'easemob_password', 'easemob_uuid', 'is_online', 'last_seen_at']);
        });
    }
};