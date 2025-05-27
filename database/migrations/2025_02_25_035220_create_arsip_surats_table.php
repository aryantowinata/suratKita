<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('arsip_surats', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat');
            $table->string('pengirim')->nullable();
            $table->string('penerima')->nullable();
            $table->string('perihal');
            $table->date('tanggal_surat');
            $table->string('jenis_surat'); // masuk atau keluar
            $table->string('file_surat')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('arsip_surats');
    }
};
