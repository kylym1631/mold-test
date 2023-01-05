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
        Schema::create('work_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->foreignId('candidate_id');
            $table->foreignId('client_id')->nullable();
            $table->decimal('rate', 10, 2)->nullable();
            $table->decimal('salary', 10, 2)->nullable();
            $table->decimal('premium', 10, 2)->nullable();
            $table->decimal('fine', 10, 2)->nullable();
            $table->decimal('bhp_form', 10, 2)->nullable();
            $table->decimal('stay_cards_cost', 10, 2)->nullable();
            $table->decimal('housing', 10, 2)->nullable();
            $table->text('recommendation')->nullable();
            $table->text('transport')->nullable();
            $table->text('work_permits')->nullable();
            $table->date('period')->nullable();
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
        Schema::dropIfExists('work_logs');
    }
};
