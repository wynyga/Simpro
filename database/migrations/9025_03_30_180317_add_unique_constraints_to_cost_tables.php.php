<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        // Unique constraint untuk cost_centres
        Schema::table('cost_centres', function (Blueprint $table) {
            $table->unique(['cost_centre_code', 'perumahan_id'], 'unique_cost_code_perumahan');
        });

        // Unique constraint untuk cost_elements
        Schema::table('cost_elements', function (Blueprint $table) {
            $table->unique(['cost_element_code', 'perumahan_id'], 'unique_element_code_perumahan');
        });

        // Unique constraint untuk cost_tees
        Schema::table('cost_tees', function (Blueprint $table) {
            $table->unique(['cost_tee_code', 'perumahan_id'], 'unique_tee_code_perumahan');
        });
    }

    public function down()
    {
        // Drop unique constraint dari cost_centres
        Schema::table('cost_centres', function (Blueprint $table) {
            $table->dropUnique('unique_cost_code_perumahan');
        });

        // Drop unique constraint dari cost_elements
        Schema::table('cost_elements', function (Blueprint $table) {
            $table->dropUnique('unique_element_code_perumahan');
        });

        // Drop unique constraint dari cost_tees
        Schema::table('cost_tees', function (Blueprint $table) {
            $table->dropUnique('unique_tee_code_perumahan');
        });
    }
};
