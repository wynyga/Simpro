<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('blok', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perumahan_id')->constrained('perumahan')->onDelete('cascade');
            $table->string('nama_blok');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('blok');
    }
};
