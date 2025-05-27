<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('disposisi_instruksi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('disposisi_id')->constrained('disposisis')->onDelete('cascade');
            $table->foreignId('instruksi_id')->constrained('instruksis')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('disposisi_instruksi');
    }
};