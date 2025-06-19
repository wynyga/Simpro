<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('cost_elements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('perumahan_id')->constrained('perumahan')->onDelete('cascade');

            // --- THIS IS THE KEY CHANGE ---
            // Change ->index() to ->unique() if it's not the primary key,
            // or to ->primary() if it is the primary key for cost_elements.
            $table->string('cost_element_code')->unique(); // Make it unique

            $table->string('cost_centre_code')->index();
            $table->string('description');
            $table->timestamps();
            $table->foreign('cost_centre_code')->references('cost_centre_code')->on('cost_centres')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('cost_elements');
    }
};