<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('unit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blok_id')->constrained('blok')->onDelete('cascade');
            $table->foreignId('tipe_rumah_id')->constrained('tipe_rumah')->onDelete('cascade');
            $table->string('nomor_unit');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('unit');
    }
};
