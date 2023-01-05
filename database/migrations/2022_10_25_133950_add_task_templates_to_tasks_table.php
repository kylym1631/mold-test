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
        Schema::table('tasks', function (Blueprint $table) {
            $table->foreignId('task_template_id')->nullable()->after('model_obj_id');
            $table->string('task_template_step_id')->nullable()->after('task_template_id');
            $table->string('task_group')->nullable()->after('task_template_step_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('task_template_id');
            $table->dropColumn('task_template_step_id');
            $table->dropColumn('task_group');
        });
    }
};
