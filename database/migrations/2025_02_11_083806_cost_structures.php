<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('cost_structures', function (Blueprint $table) {
            $table->id();
            $table->string('cost_tee_code'); // KO0100501
            $table->string('cost_code'); // KASIN / KASOUT
            $table->string('description'); // ARUS KAS MASUK / KELUAR
            $table->timestamps();

            $table->foreign('cost_tee_code')->references('cost_tee_code')->on('cost_tees')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cost_structures');
    }
};

