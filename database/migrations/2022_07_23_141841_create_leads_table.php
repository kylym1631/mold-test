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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->timestamp('date')->nullable();
            $table->string('source')->nullable();
            $table->string('company')->nullable();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('viber')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->foreignId('candidate_id')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->text('status_comment')->nullable();
            $table->tinyInteger('count_failed_call')->nullable()->default(0);
            $table->tinyInteger('count_not_liquidity')->nullable()->default(0);
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
        Schema::dropIfExists('leads');
    }
};
