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
        Schema::table('arsip_surats', function (Blueprint $table) {
            $table->string('status')->default('baru'); // Tambahkan kolom status dengan default 'baru'
        });
    }

    public function down()
    {
        Schema::table('arsip_surats', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
