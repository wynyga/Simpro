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
        Schema::create('user_perumahan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_user');
            $table->string('alamat_user');
            $table->string('no_telepon');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('user');
    }
};
