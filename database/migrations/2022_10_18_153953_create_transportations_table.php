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
        Schema::create('transportations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->string('title')->nullable();
            $table->timestamp('departure_date')->nullable();
            $table->string('departure_place')->nullable();
            $table->timestamp('arrival_date')->nullable();
            $table->foreignId('arrival_place_id')->nullable();
            $table->foreignId('driver_id')->nullable();
            $table->string('number')->nullable();
            $table->smallInteger('places_count')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('active')->nullable()->default(1);
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
        Schema::dropIfExists('transportations');
    }
};
