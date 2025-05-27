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
        Schema::create('disposisis', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_surat'); // Tidak menggunakan foreign key
            $table->enum('jenis_surat', ['masuk', 'keluar']); // Untuk membedakan surat masuk/keluar
            $table->foreignId('id_pengirim')->nullable()->constrained('users')->onDelete('cascade'); // Tambahkan nullable()
            $table->foreignId('id_penerima')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('id_bidang')->nullable()->constrained('bidangs')->onDelete('cascade');
            $table->timestamps();
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('disposisis');
    }
};