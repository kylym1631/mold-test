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
        Schema::create('client_positions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id');
            $table->string('title')->nullable();
            $table->decimal('rate', 10, 2)->nullable();
            $table->decimal('rate_after', 10, 2)->nullable();
            $table->decimal('personal_rate', 10, 2)->nullable();
            $table->text('description')->nullable();
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
        Schema::dropIfExists('client_positions');
    }
};
