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
        Schema::table('work_log_days', function (Blueprint $table) {
            $table->smallInteger('work_time')->nullable()->after('work_log_id');
            $table->dropColumn('hours');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('work_log_days', function (Blueprint $table) {
            $table->dropColumn('work_time');
        });
    }
};
