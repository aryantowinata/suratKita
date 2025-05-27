<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email'); // Nomor WhatsApp
            $table->string('login_token', 64)->nullable()->unique()->after('foto_profile');
            $table->timestamp('login_token_expires_at')->nullable()->after('login_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['phone', 'login_token', 'login_token_expires_at']);
        });
    }
};