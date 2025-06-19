<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('cost_centres', function (Blueprint $table) {
            // Remove $table->id(); if cost_centre_code is the primary key
            // $table->id();

            $table->foreignId('perumahan_id')->constrained('perumahan')->onDelete('cascade');

            // --- THIS IS THE KEY CHANGE ---
            $table->string('cost_centre_code')->primary(); // Make it the primary key

            $table->string('description');
            $table->string('cost_code'); // Assuming 'cost_code' is another column
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cost_centres');
    }
};