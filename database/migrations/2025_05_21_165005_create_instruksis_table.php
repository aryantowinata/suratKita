<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInstruksisTable extends Migration
{
    public function up()
    {
        Schema::create('instruksis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_instruksi');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('instruksis');
    }
}