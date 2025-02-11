<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('cost_elements', function (Blueprint $table) {
            $table->id();
            $table->string('cost_element_code')->unique(); // KO01005
            $table->string('cost_centre_code'); // KO010
            $table->string('description'); // Biaya Marketing
            $table->timestamps();

            $table->foreign('cost_centre_code')->references('cost_centre_code')->on('cost_centres')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cost_elements');
    }
};
