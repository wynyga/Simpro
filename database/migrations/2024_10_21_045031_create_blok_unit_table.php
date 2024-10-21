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
        Schema::create('blok_unit', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_tipe_rumah')->constrained('tipe_rumah')->onDelete('cascade');
            $table->string('blok');
            $table->string('unit');
            $table->enum('status', ['Terjual', 'Belum Terjual'])->default('Belum Terjual');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('blok_unit');
    }
};
