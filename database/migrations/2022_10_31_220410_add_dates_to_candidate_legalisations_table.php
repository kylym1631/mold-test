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
        Schema::table('candidate_legalisations', function (Blueprint $table) {
            $table->string('who_issued')->nullable()->after('title');
            $table->timestamp('issue_date')->nullable()->after('who_issued');
            $table->string('number')->nullable()->after('issue_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('candidate_legalisations', function (Blueprint $table) {
            $table->dropColumn('who_issued');
            $table->dropColumn('issue_date');
            $table->dropColumn('number');
        });
    }
};
