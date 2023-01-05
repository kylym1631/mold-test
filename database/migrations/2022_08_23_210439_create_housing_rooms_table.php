<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('housing_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('housing_id');
            $table->string('number')->nullable();
            $table->smallInteger('places_count')->nullable();
            $table->smallInteger('filled_count')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('housing_rooms');
    }
};
