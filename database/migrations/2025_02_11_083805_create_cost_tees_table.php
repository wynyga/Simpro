<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('cost_tees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perumahan_id')->constrained('perumahan')->onDelete('cascade');
            $table->string('cost_tee_code')->unique(); 
            $table->string('cost_element_code')->index();            
            $table->string('description'); 
            $table->timestamps();
            $table->foreign('cost_element_code')->references('cost_element_code')->on('cost_elements')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cost_tees');
    }
};

