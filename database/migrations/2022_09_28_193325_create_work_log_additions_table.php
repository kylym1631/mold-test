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
        Schema::create('work_log_additions', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->foreignId('candidate_id')->nullable();
            $table->foreignId('work_log_id')->nullable();
            $table->decimal('amount', 10, 2)->nullable();
            $table->text('comment')->nullable();
            $table->timestamp('date')->nullable();
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
        Schema::dropIfExists('work_log_additions');
    }
};
