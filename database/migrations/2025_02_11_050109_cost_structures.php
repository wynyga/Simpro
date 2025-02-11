<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('cost_structures', function (Blueprint $table) {
            $table->id();
            $table->string('cost_tree');      // Contoh: KI010101
            $table->string('cost_element');   // Contoh: Penerimaan Booking Fee
            $table->string('cost_centre');    // Contoh: KI010
            $table->string('cost_code');      // Contoh: KASIN
            $table->text('description');      // Contoh: ARUS KAS MASUK
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('cost_structures');
    }
};

